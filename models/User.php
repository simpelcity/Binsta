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
        if (!$user->id) {
            return false;
        }

        $uploadDir = __DIR__ . '/../public/assets/uploads/';

        if ($file && !empty($file['name'])) {
            $filePath = $uploadDir . basename($file['name']);
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            if (in_array($fileType, $allowed)) {
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (!empty($user->pfp) && file_exists($uploadDir . $user->pfp)) {
                    unlink($uploadDir . $user->pfp);
                }

                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $user->pfp = $file['name']; // store filename only
                } else {
                    error_log("Upload failed: Could not move file to $filePath");
                }
            }
        }

        if (!empty($data['remove_pfp'])) {
            if (!empty($user->pfp) && file_exists($uploadDir . $user->pfp)) {
                unlink($uploadDir . $user->pfp);
            }
            $user->pfp = null;
        }

        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $user->$key = $value;
            }
        }

        return R::store($user);
    }

    public static function findUserSnippets($userId)
    {
        return R::findAll('snippets', 'user_id = ? ORDER BY created_at DESC', [$userId]);
    }

    public static function createResetToken($email)
    {
        $user = self::findByEmail($email);
        if (!$user) {
            return false;
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $user->reset_token = $token;
        $user->reset_expires = $expires;
        R::store($user);

        return $token;
    }

    public static function findByResetToken($token)
    {
        return R::findOne('users', 'reset_token = ? AND reset_expires > NOW()', [$token]);
    }

    public static function resetPassword($token, $newPassword)
    {
        $user = self::findByResetToken($token);
        if (!$user) {
            return false;
        }

        $user->password = password_hash($newPassword, PASSWORD_BCRYPT);
        $user->reset_token = null;
        $user->toke_expiration = null;
        R::store($user);
        return true;
    }

    public static function isFollowing($followerId, $followeeId)
    {
        if (!$followerId || !$followeeId) {
            return false;
        }

        $follow = R::findOne('follows', 'follower_id = ? AND followee_id = ?', [$followerId, $followeeId]);
        return (bool) $follow;
    }

    public static function follow($followerId, $followeeId)
    {
        if (!$followerId || !$followeeId || $followerId == $followeeId) {
            return false;
        }

        if (self::isFollowing($followerId, $followeeId)) return true;

        $f = R::dispense('follows');
        $f->follower_id = $followerId;
        $f->followee_id = $followeeId;
        return R::store($f);
    }

    public static function unfollow($followerId, $followeeId)
    {
        if (!$followerId || !$followeeId) {
            return false;
        }

        $follow = R::findOne('follows', 'follower_id = ? AND followee_id = ?', [$followerId, $followeeId]);
        if ($follow) {
            R::trash($follow);
            return true;
        }

        return false;
    }

    public static function toggleFollow($followerId, $followeeId)
    {
        if (self::isFollowing($followerId, $followeeId)) {
            return self::unfollow($followerId, $followeeId);
        }

        return self::follow($followerId, $followeeId);
    }

    public static function followerCount($userId)
    {
        return R::count('follows', 'followee_id = ?', [$userId]);
    }

    public static function followingCount($userId)
    {
        return R::count('follows', 'follower_id = ?', [$userId]);
    }

    public static function getFollowers($userId)
    {
        return R::findAll('follows', 'followee_id = ? ORDER BY created_at DESC', [$userId]);
    }

    public static function getFollowing($userId)
    {
        return R::findAll('follows', 'follower_id = ? ORDER BY created_at DESC', [$userId]);
    }
}
