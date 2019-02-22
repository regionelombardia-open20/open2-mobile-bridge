<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\mobile\bridge
 * @category   CategoryName
 */

namespace lispa\amos\mobile\bridge\modules\v1\actions\security;

use lispa\amos\mobile\bridge\modules\v1\models\AccessTokens;
use lispa\amos\mobile\bridge\modules\v1\models\User;
use yii\base\Exception;
use yii\helpers\Json;
use yii\rest\Action;

class ActionLogin extends Action
{

    /**
     * @param $username
     * @param $password
     * @throws Exception
     */
    public function run()
    {
        /**@var $modelClass User */
        $modelClass = $this->modelClass;

        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        /**$LoginForm**/
        $LoginForm = new $modelClass();

        if ($bodyParams && $bodyParams["username"] && $bodyParams["password"]) {
            //Switch field
            if(property_exists($LoginForm, 'usernameOrEmail')) {
                $LoginForm->usernameOrEmail = $bodyParams["username"];
            } else {
                $LoginForm->username = $bodyParams["username"];
            }

            $LoginForm->username = $bodyParams["username"];
            $LoginForm->password = $bodyParams["password"];
            $LoginForm->ruolo = 'ADMIN';
            $tokenDevice = $bodyParams["token"];
            $osDevice = $bodyParams["os"];

            if ($LoginForm->validate()) {

                $User = User::findByUsername($LoginForm->username);

                if ($User && $User->validatePassword($LoginForm->password)) {

                    $User->refreshAccessToken($tokenDevice, $osDevice);

                    $User->save();
//return $User->extraFields();
                    $result = $User->toArray(
                        [
                            'id',
                            'username',
                            'email',
                            'accessToken',
                            'fcmToken',
                            'slimProfile',
                            'userImage',
                        ]
                    );

                    $result['access_token'] = $result['accessToken'];

                    return $result;
                }
            } else {
                //throw new Exception('Unable to Load Data');
                //return ($bodyParams);
                return $LoginForm->getErrors();
            }
        } else {
            return $bodyParams['username'];

            throw new Exception('Username or Password Missing');
        }

        throw new Exception('Username o password errati');
    }
}