<?php

namespace Binsta\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use RedBeanPHP\R as R;

R::setup(
    'mysql:host=localhost;dbname=binsta;charset=utf8mb4',
    'bit_academy',
    'bit_academy'
);

class BaseController
{
    public function getBeanById($typeOfBean, $queryStringKey)
    {
        if (!isset($_GET[$queryStringKey]) || $_GET[$queryStringKey] === '') {
            return 'no_id';
        }
        $id = (int)$_GET[$queryStringKey];
        $bean = R::findOne($typeOfBean, 'id = ?', [$id]);
        if (!$bean) {
            return 'invalid_id';
        }
        return $bean;
    }

    public function authorizeUser()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user'] === '') {
            header('Location: /');
            exit;
        } else {
            return $_SESSION['user'];
        }
    }

    public function findAll($typeOfBean)
    {
        return R::findAll($typeOfBean, 'ORDER BY created_at DESC');
    }

    public function timeAgo(\DateTime $createdAt)
    {
        $now = new \DateTime();
        $interval = $now->diff($createdAt);

        if ($interval->y) {
            return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
        } elseif ($interval->m) {
            return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
        } elseif ($interval->d) {
            return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        } elseif ($interval->h) {
            return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        } elseif ($interval->i) {
            return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'just now';
        }
    }
}
