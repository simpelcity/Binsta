<?php

namespace Binsta\Models;

use RedBeanPHP\R as R;

class Like
{
    public static function add($userId, $snippetId)
    {
        $like = R::dispense('likes');
        $like->user_id = $userId;
        $like->snippet_id = $snippetId;
        return R::store($like);
    }

    public static function remove($userId, $snippetId)
    {
        $like = R::findOne('likes', 'user_id = ? AND snippet_id = ?', [$userId, $snippetId]);
        if ($like) {
            R::trash($like);
            return true;
        }
        return false;
    }

    public static function countBySnippetId($snippetId)
    {
        return R::count('likes', 'snippet_id = ?', [$snippetId]);
    }

    public static function userLikedSnippet($userId, $snippetId)
    {
        $like = R::findOne('likes', 'user_id = ? AND snippet_id = ?', [$userId, $snippetId]);
        return $like !== null;
    }

    public static function findBySnippetId($snippetId)
    {
        return R::findAll('likes', 'snippet_id = ?', [$snippetId]);
    }
}
