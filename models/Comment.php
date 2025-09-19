<?php

namespace Binsta\Models;

use RedBeanPHP\R as R;

class Comment
{
    public static function create($content, $userId, $snippetId, $createdAt)
    {
        $comment = R::dispense('comments');
        $comment->comment = $content;
        $comment->user_id = $userId;
        $comment->snippet_id = $snippetId;
        $comment->created_at = $createdAt;
        return R::store($comment);
    }

    public static function findBySnippetId($snippetId, $limit, $offset)
    {
        $comments = R::findAll('comments', 'snippet_id = ? ORDER BY created_at ASC LIMIT ? OFFSET ?', [$snippetId, $limit, $offset]);

        foreach ($comments as $comment) {
            $comment->author = User::findById($comment->user_id);
        }

        return $comments;
    }
}
