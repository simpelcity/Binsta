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
        $this->authorizeUser();

        $code = $_POST['code'] ?? '';
        $language = $_POST['language'] ?? '';
        $caption = $_POST['caption'] ?? '';

        if (empty($code) || empty($language) || empty($caption)) {
            http_response_code(400);

            renderPage('snippets/create.twig', [
                'title' => 'Create snippet',
                'activeController' => 'create',
                'error' => 'Please fill in all fields',
                'errorNumber' => 400,
                'old' => [
                    'code' => $code,
                    'language' => $language,
                    'caption' => $caption
                ]
            ]);
            exit;
        }

        Snippet::create($_POST['code'], $_POST['language'], $_POST['caption'], $_SESSION['user'], new \DateTime());
        header('Location: /');
        exit;
    }
}
