<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\mobile\bridge
 * @category   CategoryName
 */

namespace lispa\amos\mobile\bridge\modules\v1\controllers;


use lispa\amos\chat\models\Conversation;
use lispa\amos\core\record\Record;
use lispa\amos\mobile\bridge\modules\v1\actions\comments\ActionItemComments;
use lispa\amos\mobile\bridge\modules\v1\actions\comments\ActionItemPushComment;
use yii\db\ActiveQuery;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\Response;

class CommentsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviours = parent::behaviors();
        unset($behaviours['authenticator']);

        return ArrayHelper::merge($behaviours, [
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    'bearerAuth' => [
                        'class' => HttpBearerAuth::className(),
                    ]
                ],

            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function verbs()
    {
        return [];
    }

    public function actions()
    {
        return [
            'item-comments' => [
                'class' => ActionItemComments::className(),
                'modelClass' => Record::className(),
            ],
            'item-push-comment' => [
                'class' => ActionItemPushComment::className(),
                'modelClass' => Record::className(),
            ],
        ];
    }

}