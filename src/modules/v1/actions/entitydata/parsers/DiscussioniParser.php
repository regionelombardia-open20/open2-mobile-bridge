<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\mobile\bridge
 * @category   CategoryName
 */

namespace lispa\amos\mobile\bridge\modules\v1\actions\entitydata\parsers;

use lispa\amos\admin\models\UserProfile;
use lispa\amos\community\models\Community;
use lispa\amos\core\record\Record;
use lispa\amos\discussioni\models\DiscussioniTopic;
use lispa\amos\discussioni\models\search\DiscussioniTopicSearch;
use lispa\amos\mobile\bridge\modules\v1\models\AccessTokens;
use lispa\amos\mobile\bridge\modules\v1\models\User;
use lispa\amos\news\models\News;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\Json;

class DiscussioniParser
{
    /**
     * Get all items
     * @param $namespace
     * @param $bodyParams
     * @return array
     */
    public static function getItems($namespace, $bodyParams)
    {
        //Paginated offset
        $offset = $bodyParams['offset'] * 20;

        //Check limit is set
        $limit = (int)$bodyParams['limit'] ?: 20;

        //Instance search model
        $discussioniTopicSearch = new DiscussioniTopicSearch();

        //Use search data provider
        $dataProvider = $discussioniTopicSearch->searchOwnInterest([
            'offset' => $offset,
            'limit' => $limit > 20 ? 20 : $limit
        ]);

        //Fetch news and parse it
        $items = $dataProvider->getModels();

        //Resulting array of items
        $itemsArray = [];

        foreach ($items as $item) {
            $newItem = self::parseItem($item);

            if (!empty($newItem)) {
                //Insert New Item
                $itemsArray[] = $newItem;
            }
        }

        return $itemsArray;
    }

    /**
     * Get single discussion
     * @param $bodyParams
     * @return array
     */
    public static function getItem($bodyParams)
    {
        //Id of the record
        $identifier = $bodyParams['id'];

        //Fetch discussion and parse it
        $item = DiscussioniTopic::findOne($identifier);

        //Resulting array of items
        $itemsArray = [];

        return [self::parseItem($item)];
    }

    /**
     * Parse item and optain api designed array
     * @param $item
     * @return array
     */
    public static function parseItem($item)
    {
        //The base class name
        $baseClassName = \yii\helpers\StringHelper::basename(DiscussioniTopic::className());

        //Read permission name
        $readPremission = strtoupper($baseClassName . '_READ');

        //Edit permission name
        $editPremission = strtoupper($baseClassName . '_UPDATE');

        //Can user view element
        $canView = \Yii::$app->user->can($readPremission, ['model' => $model]);

        if ($canView) {
            //Define temp item
            $newItem = [];

            //Need id column
            $newItem['id'] = $item['id'];

            //Get the list of description fields
            $newItem['representingColumn'] = $item->representingColumn();

            //Creator profile
            $owner = UserProfile::findOne(['id' => $item->created_by]);

            //Image
            $image = $item->discussionsTopicImage;

            //Fill fields from item usable in app
            $newItem['fields'] = [
                'slug' => $item->slug,
                'titolo' => $item->titolo,
                'testo' => html_entity_decode(strip_tags($item->testo)),
                'created_at' => $item->created_at,
                'created_by' => $item->created_by,
                'comments_enabled' => true,
                'owner' => [
                    'nome' => $owner->nome,
                    'cognome' => $owner->cognome,
                    'presentazione_breve' => $owner->presentazione_breve,
                    'avatarUrl' => $owner->avatarWebUrl,
                ],
                'newsImageUrl' => $image ? \Yii::$app->getUrlManager()->createAbsoluteUrl($image->getWebUrl()) : null,
            ];

            //Remove id as is not needed
            unset($newItem['fields']['id']);

            //Can edit
            $newItem['canEdit'] = \Yii::$app->user->can($editPremission, ['model' => $model]);

            return $newItem;
        }

        return [];
    }
}