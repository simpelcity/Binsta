<?php

namespace Binsta\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use Binsta\Models\Search;
use Binsta\Models\User;

class SearchController extends BaseController
{
    public function search()
    {
        $this->authorizeUser();

        $searchTerm = $_GET['search'] ?? '';
        $users = Search::searchUsers($searchTerm);
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));
        $id = end($parts);
        $user = User::findById($id);
        $userProfile = User::findById($_SESSION['user']);

        renderPage('users/search.twig', [
            'title' => 'Search Results',
            'user' => $user,
            'userProfile' => $userProfile,
            'users' => $users,
            'searchTerm' => $searchTerm,
        ]);
    }
}
