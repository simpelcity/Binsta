<?php

use Binsta\Models\User;
use RedBeanPHP\R;

function renderPage($template, $variables)
{
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/views');
    $twig = new \Twig\Environment($loader);

    $twig->display($template, $variables);
}

function error($errorNumber, $errorMessageText = null)
{
    http_response_code($errorNumber);

    $isLoggedIn = isset($_SESSION['user']) && !empty($_SESSION['user']);

    if ($isLoggedIn && !R::testConnection()) {
        R::setup('mysql:host=127.0.0.1;dbname=binsta', 'bit_academy', 'bit_academy');
    }

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $parts = explode('/', trim($path, '/'));
    $id = end($parts);

    $user = $isLoggedIn ? User::findById($id) : null;
    $userProfile = $isLoggedIn ? User::findById($_SESSION['user']) : null;

    $layout = $isLoggedIn ? '/layouts/app.twig' : '/layouts/auth.twig';

    $defaultMessages = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Page Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    ];

    $errorMessageText = $errorMessageText ?? ($defaultMessages[$errorNumber] ?? 'Unknown Error');
    $fullMessage = "$errorNumber - $errorMessageText";

    renderpage('/error/error.twig', [
        'layout' => $layout,
        'title' => $errorNumber,
        'user' => $user,
        'userProfile' => $userProfile,
        'errorNumber' => $errorNumber,
        'errorMessage' => $fullMessage,
        'controllerName' => 'Error - ' . $errorNumber,
    ]);

    exit;
}
