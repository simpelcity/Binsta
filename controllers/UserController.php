<?php

namespace Binsta\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use Binsta\Models\User;
use RedBeanPHP\R;

class UserController extends BaseController
{
    public function profile()
    {
        $this->authorizeUser();
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));
        $id = end($parts);
        $user = User::findById($id);
        $userProfile = User::findById($_SESSION['user']);
        $snippets = User::findUserSnippets($id);

        foreach ($snippets as $snippet) {
            $snippet->comment_count = R::count('comments', 'snippet_id = ?', [$snippet->id]);
            $snippet->like_count = R::count('likes', 'snippet_id = ?', [$snippet->id]);
        }

        renderPage('users/profile.twig', [
            'title' => $user->username . "'s Profile",
            'user' => $user,
            'userProfile' => $userProfile,
            'snippets' => $snippets,
        ]);
    }

    public function edit()
    {
        $this->authorizeUser();
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));
        $id = end($parts);
        $user = User::findById($id);
        $userProfile = User::findById($_SESSION['user']);

        $message = $_SESSION['flash_message'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        renderPage('users/edit.twig', [
            'title' => 'Edit profile',
            'user' => $user,
            'userProfile' => $userProfile,
            'message' => $message,
            'error' => $error,
        ]);
    }

    public function editPost()
    {
        $this->authorizeUser();

        $userId = $_SESSION['user'];

        $data = [
            'username' => $_POST['username'] ?? null,
            'name' => $_POST['name'] ?? null,
            'email' => $_POST['email'] ?? null,
            'bio' => $_POST['bio'] ?? null,
            'remove_pfp' => $_POST['remove_pfp'] ?? 0
        ];

        $result = User::update($userId, $data, $_FILES['pfp'] ?? null);

        if ($result) {
            $_SESSION['flash_message'] = 'Updated profile successfully';
            $_SESSION['flash_error'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Failed to update profile';
            $_SESSION['flash_error'] = 'danger';
        }

        header("Location: /user/edit/$userId");
        exit;
    }

    public function password()
    {
        $this->authorizeUser();
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));
        $id = end($parts);
        $user = User::findById($id);
        $userProfile = User::findById($_SESSION['user']);

        $message = $_SESSION['flash_message'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        renderPage('users/change_password.twig', [
            'title' => 'Change Password',
            'user' => $user,
            'userProfile' => $userProfile,
            'message' => $message,
            'error' => $error,
        ]);
    }

    public function passwordpost()
    {
        $this->authorizeUser();
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));
        $id = end($parts);
        $user = User::findById($id);
        $userProfile = User::findById($_SESSION['user']);

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPassword, $userProfile->password)) {
            $_SESSION['flash_message'] = 'Current password is incorrect.';
            $_SESSION['flash_error'] = 'danger';
        } elseif ($newPassword !== $confirmPassword) {
            $_SESSION['flash_message'] = 'New password and confirmation do not match.';
            $_SESSION['flash_error'] = 'danger';
        } else {
            $userProfile->password = password_hash($newPassword, PASSWORD_DEFAULT);
            R::store($userProfile);
            $_SESSION['flash_message'] = 'Password changed successfully!';
            $_SESSION['flash_error'] = 'success';
        }

        header("Location: /user/password/$id");
        exit;
    }
}
