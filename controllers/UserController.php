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
        }

        renderPage('users/profile.twig', [
            'title' => $user->username . "'s Profile",
            'user' => $user,
            'userProfile' => $userProfile,
            'snippets' => $snippets,
        ]);
    }
}
