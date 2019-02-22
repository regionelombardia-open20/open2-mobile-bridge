<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\mobile\bridge
 * @category   CategoryName
 */

namespace lispa\amos\mobile\bridge;

use lispa\amos\chat\AmosChat;
use lispa\amos\chat\models\Message;
use lispa\amos\comments\models\Comment;
use lispa\amos\comments\models\CommentReply;
use lispa\amos\core\module\AmosModule;
use lispa\amos\mobile\bridge\controllers\NotificationController;
use lispa\amos\mobile\bridge\modules\v1\models\AccessTokens;
use lispa\amos\mobile\bridge\modules\v1\models\ChatMessages;
use lispa\amos\mobile\bridge\modules\v1\models\User;
use lispa\amos\notificationmanager\AmosNotify;
use lispa\amos\notificationmanager\behaviors\NotifyBehavior;
use lispa\amos\notificationmanager\models\Notification;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\Application;
use paragraph1\phpFCM\Recipient\Device;

/**
 * Class Module
 * @package lispa\amos\mobile\bridge
 */
class Module extends AmosModule implements BootstrapInterface
{
    public static $CONFIG_FOLDER = 'config';

    /**
     * @inheritdoc
     */
    static $name = 'amosmobilebridge';

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'lispa\amos\mobile\bridge\controllers';

    public $timeout = 180;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        //Configuration
        $config = require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
        \Yii::configure($this, ArrayHelper::merge($config, $this));

        //if(\Yii::$app->controller && get_class(\Yii::$app->controller->module) == self::className()) {
        //Override user identity
        \Yii::$app->set('user', $this->user);

        //Override request component
        \Yii::$app->set('request', $this->request);
        //}
    }

    public function bootstrap($app)
    {
        if ($app instanceof Application) {
            $notificationController = new NotificationController('notifications', $this);

            Event::on(ActiveRecord::className(), ActiveRecord::EVENT_AFTER_INSERT, [$notificationController, 'afterActiveRecordCreate']);
        }
    }

    public static function getModuleName()
    {
        return self::$name;
    }

    public function getWidgetIcons()
    {
        return [
        ];
    }

    public function getWidgetGraphics()
    {
        return [
        ];
    }

    protected function getDefaultModels()
    {
        return [
        ];
    }
}
