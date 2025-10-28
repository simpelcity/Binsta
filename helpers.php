<?php

use Binsta\Models\User;

function renderPage($template, $variables)
{
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/views');
    $twig = new \Twig\Environment($loader);

    $twig->display($template, $variables);
}

function error($errorNumber, $errorMessage)
{
    http_response_code($errorNumber);

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $parts = explode('/', trim($path, '/'));
    $id = end($parts);

    $user = User::findById($id);
    $userProfile = User::findById($_SESSION['user']);

    renderpage('error.twig', [
        'title' => 'Error - ' . $errorNumber,
        'user' => $user,
        'userProfile' => $userProfile,
        'errorNumber' => $errorNumber,
        'errorMessage' => $errorMessage,
        'controllerName' => 'Error - ' . $errorNumber,
    ]);

    exit;
}
