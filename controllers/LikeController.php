<?php

namespace Binsta\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use Binsta\Models\Like;

class LikeController extends BaseController
{
    public function addPost()
    {
        $this->authorizeUser();

        $snippetId = $_POST['snippet_id'];
        $userId = $_SESSION['user'];

        $like = Like::userLikedSnippet($userId, $snippetId);

        if ($like) {
            Like::remove($userId, $snippetId);
        } else {
            Like::add($userId, $snippetId);
        }

        $likeCount = Like::countBySnippetId($snippetId);

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
