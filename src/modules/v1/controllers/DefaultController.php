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

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\attachments\components\FileImport;
use lispa\amos\core\user\User;
use lispa\amos\socialauth\models\SocialAuthUsers;
use lispa\amos\socialauth\Module;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;
use yii\web\UrlManager;
use yii\helpers\ArrayHelper;

/**
 * Class FileController
 * @package lispa\amos\mibile\bridge\controllers
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => HttpBearerAuth::className(),
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index',
                        ],
                        //'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'sign-in',
                            'sign-up',
                        ],
                        //'roles' => ['*']
                    ]
                ],
            ],
        ];
    }

    public function actionIndex() {
        echo "aaaaa";
    }

    public function actionSignIn()
    {
        echo "7";die;
    }
}
