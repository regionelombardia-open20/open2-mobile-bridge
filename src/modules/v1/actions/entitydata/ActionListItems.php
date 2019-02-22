<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\mobile\bridge
 * @category   CategoryName
 */

namespace lispa\amos\mobile\bridge\modules\v1\actions\entitydata;

use lispa\amos\admin\models\UserProfile;
use lispa\amos\community\models\Community;
use lispa\amos\core\record\Record;
use lispa\amos\discussioni\models\DiscussioniTopic;
use lispa\amos\mobile\bridge\modules\v1\actions\entitydata\parsers\DiscussioniParser;
use lispa\amos\mobile\bridge\modules\v1\actions\entitydata\parsers\ItemsParser;
use lispa\amos\mobile\bridge\modules\v1\actions\entitydata\parsers\NewsParser;
use lispa\amos\mobile\bridge\modules\v1\models\AccessTokens;
use lispa\amos\mobile\bridge\modules\v1\models\User;
use lispa\amos\news\models\News;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\rest\Action;

class ActionListItems extends Action
{

    public function run()
    {
        //Request params
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();

        //Refference namespace
        $namespace = $bodyParams['namespace'];

        //Default definition items array
        $itemsArray = [];

        switch ($namespace) {
            case News::className():
                {
                    $itemsArray = NewsParser::getItems($namespace, $bodyParams);
                }
                break;
            case DiscussioniTopic::className():
                {
                    $itemsArray = DiscussioniParser::getItems($namespace, $bodyParams);
                }
                break;
            default:
                {
                    $itemsArray = ItemsParser::getItems($namespace, $bodyParams);
                }
        }

        return $itemsArray;
    }
}