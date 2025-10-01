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
        $user = User::findById($_SESSION['user']);
        $searchTerm = $_GET['search'] ?? '';
        $users = Search::searchUsers($searchTerm);

        renderPage('users/search.twig', [
            'title' => 'Search Results',
            'user' => $user,
            'users' => $users,
            'searchTerm' => $searchTerm,
        ]);
    }
}
