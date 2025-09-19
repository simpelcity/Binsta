<?php

namespace Binsta\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use Binsta\Models\User;

class AuthController extends BaseController
{
    public function login()
    {
        renderPage('users/login.twig', [
            'title' => 'Login',
        ]);
    }

    public function loginPost()
    {
        if (
            empty($_POST['email']) ||
            empty($_POST['password'])
        ) {
            renderPage('users/login.twig', [
                'title' => 'Login',
                'error' => 'Please fill in all fields',
            ]);
            return;
        }

        $user = User::findByEmail($_POST['email']);

        if (!$user || !password_verify($_POST['password'], $user->password)) {
            renderPage('users/login.twig', [
                'title' => 'Login',
                'error' => 'Invalid username or password',
            ]);
            return;
        }

        $_SESSION['user'] = $user->id;
        header('Location: /');
        exit;
    }

    public function logout()
    {
        session_destroy();
        header('Location: /');
        exit;
    }

    public function register()
    {
        renderPage('users/register.twig', [
            'title' => 'Register',
        ]);
    }

    public function registerPost()
    {
        if (
            empty($_POST['username']) ||
            empty($_POST['password']) ||
            empty($_POST['email'])
        ) {
            renderPage('users/register.twig', [
                'title' => 'Register',
                'error' => 'All fields are required',
            ]);
            return;
        }

        if (User::findByEmail($_POST['email'])) {
            renderPage('users/register.twig', [
                'title' => 'Register',
                'error' => 'Email is already in use',
            ]);
            return;
        }

        if (User::findByUsername($_POST['username'])) {
            renderPage('users/register.twig', [
                'title' => 'Register',
                'error' => 'Username is already taken',
            ]);
        }

        $userId = User::create($_POST['username'], $_POST['email'], $_POST['password']);
        $_SESSION['user'] = $userId;
        header('Location: /');
        exit;
    }
}
