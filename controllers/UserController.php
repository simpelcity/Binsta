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

        renderPage('users/edit.twig', [
            'title' => 'Edit profile',
            'user' => $user,
            'userProfile' => $userProfile,
        ]);
    }

    public function editPost()
    {
        $userId = $_SESSION['user'];

        $data = [
            'username' => $_POST['username'] ?? null,
            'email'    => $_POST['email'] ?? null,
            'bio'      => $_POST['bio'] ?? null,
            'remove_pfp' => $_POST['remove_pfp'] ?? 0
        ];

        $result = User::update($userId, $data, $_FILES['pfp'] ?? null);

        if ($result) {
            header('Location: /');
            exit;
        } else {
            renderPage('users/edit.twig', [
                'title' => 'Edit Profile',
                'error' => 'Failed to update profile'
            ]);
        }
    }

    public function removePhoto()
    {
        $userId = $_SESSION['user'];

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        User::removePhoto($userId);

        // Redirect back to the edit/profile page
        header('Location: /user/edit');
        exit;
    }
}
