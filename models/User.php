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

    public static function update($id, $data, $file = null)
    {
        $user = R::load('users', $id);
        if (!$user->id) return false;

        // Handle image upload
        if ($file && !empty($file['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $uploadDir = 'assets/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (!empty($user->pfp) && file_exists($uploadDir . $user->pfp)) {
                    unlink($uploadDir . $user->pfp);
                }

                $fileName = uniqid('pfp_') . '.' . $ext;
                $filePath = $uploadDir . $fileName;


                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $user->pfp = $fileName; // store filename only
                }
            }
        }

        // Update text fields
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $user->$key = $value;
            }
        }

        return R::store($user);
    }

    public static function removePhoto($id)
    {
        $user = R::load('users', $id);
        if (!$user->id) return false;

        $uploadDir = 'assets/uploads/';

        if (!empty($user->pfp) && file_exists($uploadDir . $user->pfp)) {
            unlink($uploadDir . $user->pfp);
        }

        $user->pfp = null;
        R::store($user);

        return true;
    }

    public static function findUserSnippets($userId)
    {
        return R::findAll('snippets', 'user_id = ? ORDER BY created_at DESC', [$userId]);
    }
}
