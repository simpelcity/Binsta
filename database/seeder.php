<?php

require_once __DIR__ . '/../vendor/autoload.php';

use RedBeanPHP\R as R;

$host = '127.0.0.1';
$dbname = 'binsta';
$username = 'bit_academy';
$password = 'bit_academy';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $conn = new PDO($dsn, $username, $password, $options);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    throw new PDOException("Connection failed: " . $error->getMessage(), (int)$error->getCode());
}

R::setup(
    "mysql:host=$host;dbname=$dbname;charset=$charset",
    $username,
    $password
);


$users = [
    [
        'username' => 'Simpelcity',
        'email' => 'simpelcity@example.com',
        'password' => password_hash('admin', PASSWORD_BCRYPT),
    ],
    [
        'username' => 'BitAcademy',
        'email' => 'bitacademy@example.com',
        'password' => password_hash('bit_academy', PASSWORD_BCRYPT),
    ]
];

R::exec('SET FOREIGN_KEY_CHECKS = 0;');

R::wipe('comments');
R::wipe('likes');
R::wipe('snippets');
R::wipe('users');

R::exec('SET FOREIGN_KEY_CHECKS = 1;');

foreach ($users as $data) {
    $user = R::dispense('users');
    $user->username = $data['username'];
    $user->email = $data['email'];
    $user->password = $data['password'];
    R::store($user);
}

$all = [
    ['type' => 'user', 'items' => $users, 'label' => 'user'],
];

foreach ($all as $group) {
    echo "Wiped table " . $group['type'] . PHP_EOL;
}

foreach ($all as $group) {
    $count = count($group['items']);
    $label = $count === 1 ? $group['label'] : $group['label'] . 's';
    echo "Inserted $count $label" . PHP_EOL;
}
