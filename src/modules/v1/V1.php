<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\mobile\bridge
 * @category   CategoryName
 */

namespace lispa\amos\mobile\bridge\modules\v1;

use lispa\amos\core\module\AmosModule;
use lispa\amos\mobile\bridge\modules\v1\models\User;
use yii\helpers\ArrayHelper;

/**
 * Class Module
 * @package lispa\amos\mobile\bridge
 */
class V1 extends AmosModule
{
    public static $CONFIG_FOLDER = 'config';

    /**
     * @inheritdoc
     */
    static $name = 'v1';

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'lispa\amos\mobile\bridge\modules\v1\controllers';

    /**
     * @return static
     */
    public function getUser() {
        //Header with token
        $authHeader = \Yii::$app->getRequest()->getHeaders()->get('Authorization');
        preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches);

        return User::findOne([
            'access_token' => $matches[1],
            //'logout_at' => null
        ]);
    }

    /**
     * Module name
     * @return string
     */
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
