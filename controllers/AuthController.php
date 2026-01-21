<?php

namespace Binsta\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use Binsta\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../', ['.env.local']);
$dotenv->load();

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

  public function forgotPassword()
  {

    $message = $_SESSION['flash_message'] ?? null;
    $error = $_SESSION['flash_error'] ?? null;
    unset($_SESSION['flash_message'], $_SESSION['flash_error']);

    if (!empty($_SESSION)) {
      header('Location: /');
      exit;
    }

    renderPage('users/forgot_password.twig', [
      'title' => 'Forgot Password',
      'message' => $message,
      'error' => $error,
    ]);
  }

  public function forgotPasswordPost()
  {
    $email = $_POST['email'] ?? '';

    if (!$email) {
      $_SESSION['flash_message'] = 'Please enter your email.';
      $_SESSION['flash_error'] = 'danger';
      header("Location: /auth/forgot-password");
      exit;
    }

    $token = User::createResetToken($email);

    if ($token) {
      $resetLink = "http://binsta.nexed.com/auth/reset-password?token=$token";

      if ($this->sendMail($email, "Reset your password", "Click this link to reset your password:$resetLink")) {
        $_SESSION['flash_message'] = 'A password reset link has been sent to your email.';
        $_SESSION['flash_error'] = 'success';
      } else {
        $_SESSION['flash_message'] = 'Failed to send reset email. Please try again later.';
        $_SESSION['flash_error'] = 'danger';
      }


      $_SESSION['flash_message'] = 'A password reset link has been sent to your email.';
      $_SESSION['flash_error'] = 'success';
    } else {
      $_SESSION['flash_message'] = 'No user found with that email.';
      $_SESSION['flash_error'] = 'danger';
    }

    header("Location: /auth/forgot-password");
    exit;
  }

  public function resetPassword()
  {
    $token = $_GET['token'] ?? '';
    $user = User::findByResetToken($token);

    if (!$user) {
      $_SESSION['flash_message'] = 'Invalid or expired reset token.';
      $_SESSION['flash_error'] = 'danger';
      header("Location: /auth/forgot-password");
      exit;
    }

    renderPage('users/reset_password.twig', [
      'title' => 'Reset Password',
      'token' => $token
    ]);
  }

  public function resetPasswordPost()
  {
    $token = $_POST['token'] ?? '';

    $password = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
      $_SESSION['flash_message'] = 'Passwords do not match.';
      $_SESSION['flash_error'] = 'danger';
      header("Location: /auth/reset-password?token=$token");
      exit;
    }

    $result = User::resetPassword($token, $password);

    if ($result) {
      $_SESSION['flash_message'] = 'Password reset successful. You can now log in.';
      $_SESSION['flash_error'] = 'success';

      renderPage('users/reset_successfull.twig', [
        'title' => 'Reset Successfull',
        'session' => $_SESSION,
      ]);
      unset($_SESSION['flash_message'], $_SESSION['flash_error']);
      exit;
    } else {
      $_SESSION['flash_message'] = 'Invalid or expired token.';
      $_SESSION['flash_error'] = 'danger';
      header("Location: /auth/forgot-password");
    }

    exit;
  }

  public function sendMail($toEmail, $subject, $body)
  {
    $GMAIL_ADDRESS = $_ENV['GMAIL_ADDRESS'];
    $GOOGLE_APP_PASSWORD = $_ENV['GOOGLE_APP_PASSWORD'];
    $mail = new PHPMailer(true);

    try {
      $mail->SMTPDebug = 2;
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = $GMAIL_ADDRESS;
      $mail->Password = $GOOGLE_APP_PASSWORD;
      $mail->SMTPSecure = 'ssl';
      $mail->Port = 465;

      $mail->setFrom($GMAIL_ADDRESS, 'Mail');
      $mail->addAddress($toEmail);

      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $body;

      $mail->send();
      return true;
    } catch (Exception $e) {
      error_log("Email sending failed: {$mail->ErrorInfo}");
      return false;
    }
  }
}
