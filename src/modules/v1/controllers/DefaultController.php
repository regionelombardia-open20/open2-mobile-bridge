<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\mobile\bridge
 * @category   CategoryName
 */

namespace open20\amos\mobile\bridge\modules\v1\controllers;

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\models\UserProfile;
use open20\amos\attachments\components\FileImport;
use open20\amos\core\user\User;
use open20\amos\socialauth\models\SocialAuthUsers;
use open20\amos\socialauth\Module;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;
use yii\web\UrlManager;
use yii\helpers\ArrayHelper;

/**
 * Class FileController
 * @package open20\amos\mibile\bridge\controllers
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
