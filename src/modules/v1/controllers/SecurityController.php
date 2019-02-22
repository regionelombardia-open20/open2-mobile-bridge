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

use lispa\amos\mobile\bridge\modules\v1\actions\security\ActionVerifyAuth;
use lispa\amos\mobile\bridge\modules\v1\models\AccessTokens;
use lispa\amos\admin\models\LoginForm;
use lispa\amos\mobile\bridge\modules\v1\actions\security\ActionLogin;
use lispa\amos\mobile\bridge\modules\v1\actions\security\ActionLogout;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

class SecurityController extends Controller
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
                'optional' => [
                    'login',
                    'verify-auth'
                ],
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
        return [
            'login' => ['post'],
            'logout' => ['post'],
            'verify-auth' => ['post']
        ];
    }

    public function actions()
    {
        return [
            'login' => [
                'class' => ActionLogin::className(),
                'modelClass' => LoginForm::className(),
            ],
            'logout' => [
                'class' => ActionLogout::className(),
                'modelClass' => \lispa\amos\mobile\bridge\modules\v1\models\AccessTokens::className(),
            ],
            'verify-auth' => [
                'class' => ActionVerifyAuth::className(),
                'modelClass' => \lispa\amos\mobile\bridge\modules\v1\models\AccessTokens::className(),
            ],
        ];
    }
}