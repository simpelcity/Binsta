<?php

namespace Binsta\Models;

use RedBeanPHP\R as R;

class Snippet
{
    public static function findById($id)
    {
        return R::load('snippets', $id);
    }

    public static function create($code, $language, $caption, $userId, $createdAt)
    {
        $snippet = R::dispense('snippets');
        $snippet->code = $code;
        $snippet->language = $language;
        $snippet->caption = $caption;
        $snippet->user_id = $userId;
        $snippet->created_at = $createdAt;
        return R::store($snippet);
    }

    public static function update($id, $title, $code)
    {
        $snippet = R::load('snippets', $id);
        if ($snippet->id) {
            $snippet->title = $title;
            $snippet->code = $code;
            return R::store($snippet);
        }
        return false;
    }

    public static function delete($id)
    {
        $snippet = R::load('snippets', $id);
        if ($snippet->id) {
            return R::trash($snippet);
        }

        return false;
    }

    public static function findAllByUserId($userId)
    {
        return R::find('snippets', 'user_id = ?', [$userId]);
    }
}
