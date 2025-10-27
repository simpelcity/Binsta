<?php

namespace Binsta\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use Binsta\Models\User;
use Binsta\Models\Snippet;
use RedBeanPHP\R;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class UserController extends BaseController
{
    public function profile()
    {
        $this->authorizeUser();
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));
        $id = end($parts);
        $user = User::findById($id);
        $userProfile = User::findById($_SESSION['user']);
        $snippets = User::findUserSnippets($id);

        foreach ($snippets as $snippet) {
            $snippet->comment_count = R::count('comments', 'snippet_id = ?', [$snippet->id]);
            $snippet->like_count = R::count('likes', 'snippet_id = ?', [$snippet->id]);
        }

        // follow state + counts
        $isFollowing = false;
        $followerCount = 0;
        $followingCount = 0;
        try {
            $isFollowing = User::isFollowing($userProfile->id, $id);
            $followerCount = User::followerCount($id);
            $followingCount = User::followingCount($id);
        } catch (\Exception $error) {
            throw new \Exception("Error: " . $error->getMessage(), (int)$error->getCode());
        }

        $message = $_SESSION['flash_message'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        renderPage('users/profile.twig', [
            'title' => $user->username . "'s Profile",
            'user' => $user,
            'userProfile' => $userProfile,
            'snippets' => $snippets,
            'isFollowing' => $isFollowing,
            'followerCount' => $followerCount,
            'followingCount' => $followingCount,
            'message' => $message,
            'error' => $error,
        ]);
    }

    public function edit()
    {
        $this->authorizeUser();
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));
        $id = end($parts);
        $user = User::findById($id);
        $userProfile = User::findById($_SESSION['user']);

        $message = $_SESSION['flash_message'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        renderPage('users/edit.twig', [
            'title' => 'Edit profile',
            'user' => $user,
            'userProfile' => $userProfile,
            'message' => $message,
            'error' => $error,
        ]);
    }

    public function editPost()
    {
        $this->authorizeUser();

        $userId = $_SESSION['user'];

        $data = [
            'username' => $_POST['username'] ?? null,
            'name' => $_POST['name'] ?? null,
            'email' => $_POST['email'] ?? null,
            'bio' => $_POST['bio'] ?? null,
            'remove_pfp' => $_POST['remove_pfp'] ?? 0
        ];

        $result = User::update($userId, $data, $_FILES['pfp'] ?? null);

        if ($result) {
            $_SESSION['flash_message'] = 'Updated profile successfully';
            $_SESSION['flash_error'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Failed to update profile';
            $_SESSION['flash_error'] = 'danger';
        }

        header("Location: /user/edit/$userId");
        exit;
    }

    public function change_password()
    {
        $this->authorizeUser();
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));
        $id = end($parts);
        $user = User::findById($id);
        $userProfile = User::findById($_SESSION['user']);

        $message = $_SESSION['flash_message'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        renderPage('users/change_password.twig', [
            'title' => 'Change Password',
            'user' => $user,
            'userProfile' => $userProfile,
            'message' => $message,
            'error' => $error,
        ]);
    }

    public function change_passwordpost()
    {
        $this->authorizeUser();
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));
        $id = end($parts);
        $userProfile = User::findById($_SESSION['user']);

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPassword, $userProfile->password)) {
            $_SESSION['flash_message'] = 'Current password is incorrect.';
            $_SESSION['flash_error'] = 'danger';
        } elseif ($newPassword !== $confirmPassword) {
            $_SESSION['flash_message'] = 'New password and confirmation do not match.';
            $_SESSION['flash_error'] = 'danger';
        } else {
            $userProfile->password = password_hash($newPassword, PASSWORD_DEFAULT);
            R::store($userProfile);
            $_SESSION['flash_message'] = 'Password changed successfully!';
            $_SESSION['flash_error'] = 'success';
        }

        header("Location: /user/password/$id");
        exit;
    }

    public function forgot_password()
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

    public function forgot_passwordPost()
    {
        $email = $_POST['email'] ?? '';

        if (!$email) {
            $_SESSION['flash_message'] = 'Please enter your email.';
            $_SESSION['flash_error'] = 'danger';
            header("Location: /user/forgot-password");
            exit;
        }

        $token = User::createResetToken($email);

        if ($token) {
            $resetLink = "httpd://binsta.nexed.com/user/reset-password?token=$token";

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

        header("Location: /user/forgot-password");
        exit;
    }

    public function reset_password()
    {
        $token = $_GET['token'] ?? '';
        $user = User::findByResetToken($token);

        if (!$user) {
            $_SESSION['flash_message'] = 'Invalid or expired reset token.';
            $_SESSION['flash_error'] = 'danger';
            header("Location: /user/forgot-password");
            exit;
        }

        renderPage('users/reset_password.twig', [
            'title' => 'Reset Password',
            'token' => $token
        ]);
    }

    public function reset_passwordPost()
    {
        $token = $_POST['token'] ?? '';

        $password = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($password !== $confirm) {
            $_SESSION['flash_message'] = 'Passwords do not match.';
            $_SESSION['flash_error'] = 'danger';
            header("Location: /user/reset-password?token=$token");
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
            header("Location: /user/forgot-password");
        }

        exit;
    }

    public function sendMail($toEmail, $subject, $body)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 2;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '<YOUR_GMAIL_ADDRESS>';
            $mail->Password = '<YOUR_GOOGLE_APP_PASSWORD>';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('<YOUR_GMAIL_ADDRESS>', 'Mail');
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

    public function followpost()
    {
        $this->authorizeUser();

        $followerId = $_SESSION['user'] ?? null;
        $followeeId = $_POST['followee_id'] ?? null;

        if (!$followerId || !$followeeId) {
            $_SESSION['flash_message'] = 'Invalid follow request.';
            $_SESSION['flash_error'] = 'danger';
            header('Location: /');
            exit;
        }

        if ($followerId == $followeeId) {
            $_SESSION['flash_message'] = 'You cannot follow yourself.';
            $_SESSION['flash_error'] = 'danger';
            header("Location: /user/profile/$followeeId");
            exit;
        }

        // Toggle follow/unfollow
        $result = User::toggleFollow($followerId, $followeeId);

        if ($result) {
            $msg = User::isFollowing($followerId, $followeeId) ? 'Followed user.' : 'Unfollowed user.';
            $_SESSION['flash_message'] = $msg;
            $_SESSION['flash_error'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Failed to update follow status.';
            $_SESSION['flash_error'] = 'danger';
        }

        header("Location: /user/profile/$followeeId");
        exit;
    }
}
