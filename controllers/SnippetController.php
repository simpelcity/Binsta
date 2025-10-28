<?php

namespace Binsta\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use Binsta\Models\User;
use Binsta\Models\Snippet;

class SnippetController extends BaseController
{
    public function create()
    {
        $this->authorizeUser();

        $user = User::findById($_SESSION['user']);
        $userProfile = User::findById($_SESSION['user']);

        renderPage('snippets/create.twig', [
            'title' => 'Create snippet',
            'activeController' => 'create',
            'user' => $user,
            'userProfile' => $userProfile,
        ]);
    }

    public function createPost()
    {
        if (
            empty($_POST['code']) ||
            empty($_POST['language']) ||
            empty($_POST['caption'])
        ) {
            renderPage('snippets/create.twig', [
                'title' => 'Create snippet',
                'activeController' => 'create',
                'error' => 'Please fill in all fields',
            ]);
        }

        Snippet::create($_POST['code'], $_POST['language'], $_POST['caption'], $_SESSION['user'], new \DateTime());
        header('Location: /');
        exit;
    }
}
