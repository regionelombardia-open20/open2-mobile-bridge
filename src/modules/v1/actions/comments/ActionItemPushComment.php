<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\mobile\bridge
 * @category   CategoryName
 */

namespace lispa\amos\mobile\bridge\modules\v1\actions\comments;

use lispa\amos\admin\models\UserProfile;
use lispa\amos\comments\models\Comment;
use lispa\amos\comments\models\CommentInterface;
use lispa\amos\community\models\Community;
use lispa\amos\core\record\Record;
use lispa\amos\mobile\bridge\modules\v1\models\AccessTokens;
use lispa\amos\mobile\bridge\modules\v1\models\User;
use yii\base\Exception;
use yii\helpers\Json;
use yii\rest\Action;

class ActionItemPushComment extends Action
{
    public function run()
    {
        //Request params
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();

        if(empty($bodyParams['comment_text'])) {
            return false;
        }

        //Refference namespace
        $namespace = $bodyParams['namespace'];

        /**
         * Class for this fetch, expected Record
         * @var $class Record
         */
        $class = new $namespace();

        //Record interested
        if (!empty($bodyParams['id'])) {
            $record = $class::findOne(['id' => $bodyParams['id']]);
        } else {
            $record = new $class();
        }

        //Comments permission
        $canComment = 'COMMENTS_CONTRIBUTOR';

        //Commentable
        if ($record instanceof CommentInterface && \Yii::$app->user->can($canComment)) {
            //If the content is commentable
            if (!$record->isCommentable()) {
                return ['commentable' => false];
            }

            //Store comment
            $comment = new Comment();
            $comment->comment_text = strip_tags($bodyParams['comment_text']);
            $comment->context = $namespace;
            $comment->context_id = $record->id;
            $comment->save(false);

            return [
                'commentable' => true
            ];
        }

        return ['commentable' => false];
    }
}