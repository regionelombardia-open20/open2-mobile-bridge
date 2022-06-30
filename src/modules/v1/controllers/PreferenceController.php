<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    [NAMESPACE_HERE]
 * @category   CategoryName
 */

namespace open20\amos\mobile\bridge\modules\v1\controllers;

use open20\amos\admin\AmosAdmin;
use open20\amos\core\user\User;
use Exception;
use preference\userprofile\models\PreferenceUserTargetAttribute;
use preference\userprofile\utility\EmailUtility;
use preference\userprofile\utility\TargetTagUtility;
use preference\userprofile\utility\UserInterestTagUtility;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Controller;

class PreferenceController extends DefaultController
{

    public function behaviors()
    {
        $behaviours = parent::behaviors();

        return ArrayHelper::merge($behaviours,
                [
                    'verbFilter' => [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'create-profile' => ['get'],
                            'topic' => ['get'],
                        ],
                    ],
        ]);
    }

    /**
     *
     * @param type $email
     * @return type
     * @throws Exception
     */
    public function actionCreateProfile($email = null)
    {
        $code        = 0;
        $message     = '';
        $transaction = null;
        $ret         = ['code' => 0, 'message' => ''];

        try {
            // controlliamo che sia già registrato l'utente...
            // se sì ed è attivo allora lo loggo...
            $user = User::find()->where(['email' => $email])->andWhere(['status' => 10])->one();
            if (!empty($user)) {
                $ret = $this->getError(1);
            }

            if (!$ret['code']) {
                $transaction = Yii::$app->db->beginTransaction();
                // non esiste lo registro

                $module           = Yii::$app->getModule(AmosAdmin::getModulename());
                $userCreatedArray = $module->createNewAccount(
                    '-', '-', $email, 1, false, null, null,
                    AmosAdmin::getModulename()
                );


                if (isset($userCreatedArray['error']) && ($userCreatedArray['error']
                    >= 1)) {
                    $ret = $this->getError(2);
                } else {

                    // utente creato vado a settare i dati inseriti in registrazione.
                    // 1 inserico i dati sul profilo
                    $user                       = $userCreatedArray['user'];
                    $user->password_reset_token = null;
                    $password                   = uniqid('', true);
                    $user->password_hash        = \Yii::$app->security->generatePasswordHash($password);
                    $user->status               = 10;
                    $user->save(false);

                    $userProfile          = $user->userProfile;
                    $userProfile->nome    = '';
                    $userProfile->cognome = '';
                    $userProfile->save(false);

                    $interest_classname = 'simple-choice';
                    $tag                = TargetTagUtility::getTargetByKey('cittadino');
                    if (!empty($tag)) {
                        UserInterestTagUtility::saveRegisteredUserInterestTag($userProfile,
                            $interest_classname, $tag);
                    } else {
                        throw new Exception('Impossibile creare target cittadino...');
                    }

                    $uta                       = new PreferenceUserTargetAttribute();
                    $uta->email                = $email;
                    $uta->validated_email_flag = false;
                    $uta->target_code          = TargetTagUtility::getTargetByKey('cittadino')->codice;
                    $uta->user_id              = $user->id;
                    $uta->save(false);
                    EmailUtility::sendUserMailQuickRegistration($email,
                        'Lombardia Informa: registrazione rapida', $email,
                        $password);
                }
                $transaction->commit();
            }
        } catch (Exception $ex) {
            $ret = ['code' => $ex->getCode(), 'message' => $ex->getMessage()];
        }
        return Json::encode($ret);
    }

    /**
     *
     * @param type $code
     */
    protected function getError($code)
    {
        $ret = [];

        switch ($code) {
            case 1:
                $ret ['message'] = 'Utente già registrato';
                $ret ['code'] = $code;
                break;
            case 2:
                $ret ['message'] = 'Impossibile creare l\'anagrafica';
                $ret ['code'] = $code;
                break;
        }
        return $ret;
    }

    /**
     *
     * @param string $cod_tematica
     * @return array
     */
    public function actionTopic($cod_tematica)
    {
        return Json::encode(['code' => 1, 'decription' => 'test']);
    }
}