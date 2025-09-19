<?php

function renderPage($template, $variables)
{
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/views');
    $twig = new \Twig\Environment($loader);

    $twig->display($template, $variables);
}

function error($errorNumber, $errorMessage)
{
    http_response_code($errorNumber);
    renderpage('error.twig', [
        'errorNumber' => $errorNumber,
        'errorMessage' => $errorMessage,
        'controllerName' => 'Error - ' . $errorNumber,
        'title' => 'Error - ' . $errorNumber
    ]);
}
