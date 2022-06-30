<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\mobile\bridge\modules\v1\controllers
 * @category   CategoryName
 */

namespace open20\amos\mobile\bridge\modules\v1\controllers;

use DateTime;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventCategory;
use open20\amos\events\models\EventLocation;
use Exception;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\log\Logger;
use yii\web\Controller;

class EventsAriaController extends Controller
{

    public function behaviors()
    {
        $behaviours = parent::behaviors();
        unset($behaviours['authenticator']);

        return ArrayHelper::merge($behaviours,
                [
                    'authenticator' => [
                        'class' => CompositeAuth::className(),
                        'authMethods' => [
                            'bearerAuth' => [
                                'class' => HttpBearerAuth::className(),
                            ]
                        ],
                    ],
                    'verbFilter' => [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'events-list' => ['get'],
                            'events-search' => ['get'],
                            'event-detail' => ['get'],
                            'event-categories' => ['get'],
                        ],
                    ],
        ]);
    }

    /**
     *
     * @param integer $offset
     * @param integer $limit
     * @param string $from_date
     * @param string $to_date
     */
    public function actionEventsList(
        $offset = null, $limit = null, $from_date = null, $to_date = null,
        $text = null, $category = null, $order = 'ASC'
    )
    {
        $list = [];
        $list_size = 0;
        try {
            $query = Event::find();
            $query->joinWith('eventLocation');
            $query->andWhere(['publish_on_prl' => 1]);
            $query->andWhere(['or',['event_id' => 0],['event_id' => null]]);
            if (!is_null($offset)) {
                $query->offset($offset);
            }
            if (!is_null($limit)) {
                $query->limit($limit);
            }
            $date_cond = "";
            if (!empty($to_date) || !empty($from_date)) {
                if (empty($to_date)) {
                    $to_date = $from_date;
                } elseif (empty($from_date)) {
                    $from_date = $to_date;
                }
                $date_cond = ['>',
                    Event::tableName().'.begin_date_hour',
                    DateTime::createFromFormat('d-m-Y', $to_date)->modify("+1 day")->format('Y-m-d')];
                $end_cond  = ['<',
                    Event::tableName().'.end_date_hour',
                    DateTime::createFromFormat('d-m-Y', $from_date)->format('Y-m-d')];

                $date_cond = ['not', ['or', $date_cond, $end_cond]];
            }
            if (!empty($date_cond)) {
                $query->andFilterWhere($date_cond);
            }
            if (!empty($category)) {
                $cat = $this->getEventCategoryByDescription($category);
                $query->andFilterWhere(['event_category_id' => $cat->id]);
            }
            if (!empty($text)) {
                $query->andFilterWhere(['or',
                    ['like', 'title', $text],
                    ['like', 'summary', $text],
                    ['like', Event::tableName() . '.description', $text],
                    ['like', EventLocation::tableName() .'.name', $text],
                ]);
            }
            $query->orderBy(['begin_date_hour' => $order == 'ASC' ? SORT_ASC : SORT_DESC]);
            $listModel = $query->all();
            foreach ($listModel as $event) {
                $the_event = $this->parseEvent($event);
                $list[]    = $the_event;
            }
            if (!is_null($limit)) {
                $query->limit(null);
                $list_size = $query->count();
            }else{
                $list_size = count($list);
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
        }
        return Json::encode(['size' => $list_size, 'list' => $list]);
    }

    /**
     *
     * @param integer $offset
     * @param integer $limit
     * @param string $from_date
     * @param string $to_date
     */
    public function actionEventsSearch(
        $offset = null, $limit = null, $from_date = null, $to_date = null,
        $text = null, $category = null, $order = 'ASC'
    )
    {
        $list = [];
        $list_size = 0;
        try {
            $query = Event::find();
            $query->joinWith('eventLocation');
            $query->andWhere(['publish_on_prl' => 1]);
            if (!is_null($offset)) {
                $query->offset($offset);
            }
            if (!is_null($limit)) {
                $query->limit($limit);
            }
            $date_cond = "";
            if (!empty($to_date) || !empty($from_date)) {
                if (empty($to_date)) {
                    $to_date = $from_date;
                } elseif (empty($from_date)) {
                    $from_date = $to_date;
                }
                $date_cond = ['>',
                    Event::tableName().'.begin_date_hour',
                    DateTime::createFromFormat('d-m-Y', $to_date)->modify("+1 day")->format('Y-m-d')];
                $end_cond  = ['<',
                    Event::tableName().'.end_date_hour',
                    DateTime::createFromFormat('d-m-Y', $from_date)->format('Y-m-d')];

                $date_cond = ['not', ['or', $date_cond, $end_cond]];
            }
            if (!empty($date_cond)) {
                $query->andFilterWhere($date_cond);
            }
            if (!empty($category)) {
                $cat = $this->getEventCategoryByDescription($category);
                $query->andFilterWhere(['event_category_id' => $cat->id]);
            }
            if (!empty($text)) {
                $query->andFilterWhere(['or',
                    ['like', 'title', $text],
                    ['like', 'summary', $text],
                    ['like', Event::tableName() . '.description', $text],
                    ['like', EventLocation::tableName() .'.name', $text],
                ]);
            }
            $query->orderBy(['begin_date_hour' => $order == 'ASC' ? SORT_ASC : SORT_DESC]);
            $listModel = $query->all();
            foreach ($listModel as $event) {
                $the_event = $this->parseEvent($event);
                $list[]    = $the_event;
            }
            if (!is_null($limit)) {
                $query->limit(null);
                $list_size = $query->count();
            }else{
                $list_size = count($list);
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
        }
        return Json::encode(['size' => $list_size, 'list' => $list]);
    }

    /**
     *
     * @param integer $id
     */
    public function actionEventDetail($id)
    {
        $the_event = [];
        try {
            $event = Event::findOne($id);
            if (!is_null($event)) {
                $the_event = $this->parseEvent($event);
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
        }
        return Json::encode($the_event);
    }

    /**
     *
     * @param mixed $event
     */
    protected function parseEvent($event)
    {
        $formatter     = \Yii::$app->formatter;
        $eventLocation = $event->eventLocation;
        $eventPlace    = $eventLocation->eventPlaces;

        $element                               = [];
        $element['id']                         = $event->id;
        $element['begin_date_hour']            = $formatter->asDatetime($event->begin_date_hour,
            "dd-MM-yyyy HH:mm:ss");
        $element['end_date_hour']              = $formatter->asDatetime($event->end_date_hour,
            "dd-MM-yyyy HH:mm:ss");
        $element['title']                      = $event->title;
        $element['programme']                  = $event->eventLanding->schedule;
        $element['summary']                    = $event->summary;
        $element['category']                   = $event->eventCategory->description;
        $element['description']                = $event->description;
        $element['event_location']             = $eventLocation->name;
        $element['event_address']              = $eventPlace->address;
        $element['event_address_house_number'] = $eventPlace->street_number;
        $element['event_address_cap']          = $eventPlace->postal_code;
        $element['city']                       = $eventPlace->city;
        $element['province']                   = $eventPlace->province;
        $element['country']                    = $eventPlace->country;
        $element['created_at']                 = $formatter->asDatetime($event->created_at,
            "dd-MM-yyyy HH:mm:ss");
        $element['created_by']                 = $event->created_by;
        $element['updated_at']                 = $formatter->asDatetime($event->updated_at,
            "dd-MM-yyyy HH:mm:ss");
        $element['updated_by']                 = $event->updated_by;
        $element['deleted_at']                 = $formatter->asDatetime($event->deleted_at,
            "dd-MM-yyyy HH:mm:ss");
        $element['deleted_by']                 = $event->deleted_by;
        $logo                                  = $event->getEventLogo();
        $element['eventImageUrl']              = (is_null($logo) ? null : $logo->getWebUrl('original',
                true));
        $element['landingUrl']                 = $event->getEventUrl();
        $element['linked_events']              = $this->getEvntSons($event);
        return $element;
    }

    /**
     * 
     */
    public function actionEventCategories()
    {
        $categories = EventCategory::find()->select('id , description')->all();
        return Json::encode($categories);
    }

    /**
     * 
     * @param type $desription
     * @return EventCategory
     */
    protected function getEventCategoryByDescription($desription)
    {
        $category = EventCategory::find()->andWhere(['description' => $desription])->one();
        return $category;
    }

    /**
     *
     * @param Event $event
     * @return array
     */
    protected function getEvntSons($event)
    {
        $list = [];
        try {
            $query = Event::find();
            $query->andWhere(['publish_on_prl' => 1]);
            $query->andWhere(['event_id' => $event->id]);

            $query->orderBy(['begin_date_hour' => $order == 'ASC' ? SORT_ASC : SORT_DESC]);
            $listModel = $query->all();
            foreach ($listModel as $event) {
                $the_event = $this->parseEvent($event);
                $list[]    = $the_event;
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
        }
        return $list;
    }
}
