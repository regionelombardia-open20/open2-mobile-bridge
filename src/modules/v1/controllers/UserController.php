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


use yii\filters\auth\CompositeAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\Response;

class UserController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'update' => ['post'],
                    'image' => ['post'],
                    'follow' => ['post'],
                    'unfollow' => ['post'],
                ],
            ],
            'authenticator' => [
                'class' => CompositeAuth::className(),
            ],
            /*
            'rateLimiter' => [
                'class' => RateLimiter::className(),
            ],
            */
        ];
    }

    public function actionFollow()
    {
        return true;
    }

    public function actionUnfollow()
    {
        return true;
    }

    public function actionUpdate()
    {
        return true;
    }

    public function actionImage()
    {
        return true;

    }

}