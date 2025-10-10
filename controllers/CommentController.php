<?php

namespace Binsta\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use Binsta\Models\Comment;
use Binsta\Models\User;

class CommentController extends BaseController
{
    public function createPost()
    {
        $this->authorizeUser();

        if (empty($_POST['comment']) || empty($_POST['snippet_id'])) {
            header('Location: /#snippet-' . $_POST['snippet_id']);
            exit;
        }

        Comment::create($_POST['comment'], $_SESSION['user'], $_POST['snippet_id'], new \DateTime());
        header('Location: /#snippet-' . $_POST['snippet_id']);
        exit;
    }

    public function getCommentsBySnippetId()
    {
        $snippetId = $_GET['snippet_id'] ?? null;
        $offset = $_GET['offset'] ?? 0;
        $limit = $_GET['limit'] ?? 2;

        if (!$snippetId) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Missing snippet_id']);
            exit;
        }

        $comments = Comment::findBySnippetId($snippetId, $limit, $offset);
        $totalComments = Comment::countBySnippetId($snippetId);

        if (empty($comments)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No comments found']);
            exit;
        }

        $formattedComments = [];
        foreach ($comments as $comment) {
            $comment->created_at = $this->timeAgo(new \DateTime($comment->created_at));
            $user = User::findById($comment->author->id);

            $formattedComments[] = [
                'user_id' => $comment->author->id,
                'username' => $comment->author->username,
                'pfp' => $user->pfp,
                'comment' => $comment->comment,
                'created_at' => $comment->created_at,
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'comments' => $formattedComments,
            'total' => $totalComments,
        ]);
        exit;
    }
}
