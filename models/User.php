<?php

namespace Binsta\Models;

use RedBeanPHP\R as R;

class User
{
    public static function findById($id)
    {
        return R::load('users', $id);
    }

    public static function findByEmail($email)
    {
        return R::findOne('users', 'email = ?', [$email]);
    }

    public static function findByUsername($username)
    {
        return R::findOne('users', 'username = ?', [$username]);
    }

    public static function create($username, $email, $password)
    {
        $user = R::dispense('users');
        $user->username = $username;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        return R::store($user);
    }

    public static function findUserSnippets($userId)
    {
        return R::findAll('snippets', 'user_id = ? ORDER BY created_at DESC', [$userId]);
    }
}
