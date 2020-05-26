<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    Open20Package
 * @category   CategoryName
 */
namespace open20\amos\mobile\bridge\modules\v1\actions\entitydata\parsers;

use open20\amos\core\models\ContentLikes;
use open20\amos\core\models\ModelsClassname;
use Yii;

class BaseParser
{

    /**
     * 
     * @param type $model
     */
    protected static function isLikeMe($model)
    {
        $likeme = false;

        $obj = $model;
        if ($obj) {
            $uid = Yii::$app->getUser()->id;
            $model_class_obj = ModelsClassname::find(['classname' => get_class($model)])->one();
            if (!is_null($model_class_obj)) {
                $likeme = ContentLikes::getLikeMe($uid, $model->id, $model_class_obj->id);
            }
        }
        return $likeme;
    }
    
    /**
     * 
     * @param type $model
     * @return type
     */
    protected static function getCountLike($model)
    {
        $countLike = 0;

        $obj = $model;
        if ($obj) {
            $uid = Yii::$app->getUser()->id;
            $model_class_obj = ModelsClassname::find(['classname' => get_class($model)])->one();
            if (!is_null($model_class_obj)) {
                $countLike = ContentLikes::getLikesToCounter(null, $model->id, $model_class_obj->id);
            }
        }
        return $countLike;
    }
}
