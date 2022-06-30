<?php
namespace open20\amos\mobile\bridge\modules\v1\controllers;

use open20\amos\favorites\AmosFavorites;
use open20\amos\favorites\widgets\FavoriteWidget;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\httpclient\Exception;
use yii\log\Logger;

class FavoriteController extends DefaultController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviours = parent::behaviors();

        return ArrayHelper::merge($behaviours, [
            'verbFilter' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'favorite' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * 
     * @return type
     */
    public function actionFavorite()
    {
        $notify = Yii::$app->getModule('notify');
        
        try {
            //Request params
            $bodyParams = Yii::$app->getRequest()->getBodyParams();

            //Refference namespace
            $classname = $bodyParams['namespace'];
            $cid = $bodyParams['id'];
            
            if ($classname) {
                $uid = \Yii::$app->getUser()->id;
                $model_class_obj = ModelsClassname::find()->andWhere(['classname' => $classname])->one();
                if (!is_null($model_class_obj)) {
                    $alreadyFavorite = $notify->isFavorite($model, $uid);
                    
                    if ($alreadyFavorite) {
                        $ok = $notify->favouriteOff($uid, $classname, $cid);

                        return $this->returnValues($ok, self::FAVORITE_OFF);
                    } else {
                        $ok = $notify->favouriteOn($uid, $classname, $cid);

                        return $this->returnValues($ok, self::FAVORITE_ON);
                    }
                }
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        
    }
    
    /**
     * Make the final return values array and then encode it in JSON
     * @param bool $ok
     * @param string $type
     * @return string
     */
    private function returnValues($ok, $type)
    {
        $retVal = [
            'success' => 0,
            'nowFavorite' => 0,
            'nowNotFavorite' => 1,
            'msg' => '',
            'favoriteBtnTitle' => ''
        ];

        $retVal['success'] = $ok ? 1 : 0;

        if ($ok && ($type == self::FAVORITE_ON)) {
            $retVal['nowFavorite'] = 1;
            $retVal['nowNotFavorite'] = 0;
            $retVal['favoriteBtnTitle'] = FavoriteWidget::favoriteBtnTitle(true);
            $retVal['msg'] = $ok 
                ? AmosFavorites::t('amosfavorites', '#successfully_added')
                : AmosFavorites::t('amosfavorites', '#error_while_adding');
        } elseif ($ok && ($type == self::FAVORITE_OFF)) {
            $retVal['nowFavorite'] = 0;
            $retVal['nowNotFavorite'] = 1;
            $retVal['favoriteBtnTitle'] = FavoriteWidget::favoriteBtnTitle(false);
            $retVal['msg'] = $ok 
                ? AmosFavorites::t('amosfavorites', '#successfully_removed')
                : AmosFavorites::t('amosfavorites', '#error_while_removing');
        }

        return $retVal;
    }


}
