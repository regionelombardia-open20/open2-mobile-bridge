<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    amos-mobile-bridge
 * @category   CategoryName
 */

namespace lispa\amos\mobile\bridge\modules\v1\models;

use lispa\amos\admin\models\UserProfile;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends \lispa\amos\core\user\User
{
    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'slimProfile';
        $fields[] = 'userImage';
        $fields[] = 'accessToken';
        $fields[] = 'fcmToken';
        return $fields;
    }

    /**
     * @return mixed
     */
    public function getSlimProfile()
    {
        return $this->profile->toArray(
            [
                'nome',
                'cognome',
                'sesso',
                'presentazione_breve'
            ]
        );
    }

    /**
     * @return string
     */
    public function getUserImage()
    {
        $userProfileImage = $this->profile->userProfileImage;

        return $userProfileImage ? $userProfileImage->getWebUrl('original', true) : '';
    }

    public function getAccessToken() {
        $token = AccessTokens::findOne(['user_id' => $this->id]);

        return $token->access_token;
    }

    public function getFcmToken() {
        $token = AccessTokens::findOne(['user_id' => $this->id]);

        return $token->fcm_token;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $Token = AccessTokens::findOne(['access_token' => $token]);

        if ($Token) {
            return $Token->user;
        }

        return false;
    }

    public function refreshAccessToken($deviceToken, $deviceOs)
    {
        $token = new AccessTokens();
        $token->user_id = $this->id;
        $token->access_token = \Yii::$app->getSecurity()->generateRandomString();
        $token->fcm_token = $deviceToken;
        $token->device_os = $deviceOs;
        $token->save(false);
        return $token;
    }
}