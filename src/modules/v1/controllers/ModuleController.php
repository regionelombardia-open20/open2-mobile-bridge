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

use lispa\amos\core\record\Record;
use lispa\amos\mobile\bridge\modules\v1\actions\entitydata\ActionEditItem;
use lispa\amos\mobile\bridge\modules\v1\actions\entitydata\ActionListItems;
use lispa\amos\mobile\bridge\modules\v1\actions\entitydata\ActionViewItem;
use lispa\amos\mobile\bridge\modules\v1\models\AccessTokens;
use lispa\amos\mobile\bridge\modules\v1\models\User;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

class ModuleController extends Controller
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
            'list-items' => [
                'class' => ActionListItems::className(),
                'modelClass' => Record::className(),
            ],
            'edit-item' => [
                'class' => ActionEditItem::className(),
                'modelClass' => Record::className(),
            ],
            'view-item' => [
                'class' => ActionViewItem::className(),
                'modelClass' => Record::className(),
            ],
        ];
    }
}