<?php

namespace Binsta\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use Binsta\Models\User;
use Binsta\Models\Snippet;
use Binsta\Models\Comment;
use Binsta\Models\Like;

class FeedController extends BaseController
{

    public function index()
    {
        $this->authorizeUser();
        $user = User::findById($_SESSION['user']);
        $snippets = $this->findAll('snippets');

        foreach ($snippets as $snippet) {
            $snippet->currentTime = $this->timeAgo(new \DateTime($snippet->created_at));
            $snippet->comments = Comment::findBySnippetId($snippet->id, 2, 0);
            $snippet->author = User::findById($snippet->user_id);
            $snippet->likes = Like::countBySnippetId($snippet->id);
            $snippet->userLiked = Like::userLikedSnippet($user->id, $snippet->id);
        }

        renderPage('snippets/feed.twig', [
            'title' => 'Feed',
            'activeController' => 'feed',
            'snippets' => $snippets,
            'user' => $user,
        ]);
    }
}
