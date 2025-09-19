<?php

require_once __DIR__ . '/../vendor/autoload.php';

use RedBeanPHP\R as R;

$host = 'localhost';
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
        'email' => 'wietse2007.3@gmail.com',
        'password' => password_hash('admin', PASSWORD_BCRYPT)
    ]
];

// R::wipe('users');
// R::wipe('snippets');
// R::wipe('likes');
// R::wipe('comments');

foreach ($users as $data) {
    $user = R::dispense('users');
    $user->username = $data['username'];
    $user->email = $data['email'];
    $user->password = $data['password'];
    R::store($user);
}

// $kitchenBeans = [];
// foreach ($kitchens as $data) {
//     $kitchen = R::dispense('kitchens');
//     $kitchen->name = $data['name'];
//     $kitchen->description = $data['description'];
//     $id = R::store($kitchen);
//     $kitchenBeans[] = $id;
// }

// foreach ($recipes as $data) {
//     $recipe = R::dispense('recipes');
//     $recipe->name = $data['name'];
//     $recipe->type = $data['type'];
//     $recipe->level = $data['level'];
//     $recipe->kitchens_id = $data['kitchens_id'];
//     R::store($recipe);
// }

// foreach ($users as $data) {
//     $user = R::dispense('users');
//     $user->username = $data['username'];
//     $user->password = $data['password'];
//     R::store($user);
// }

$all = [
    ['type' => 'user', 'items' => $users, 'label' => 'user'],
];

// foreach ($all as $group) {
//     echo "Wiped table " . $group['type'] . PHP_EOL;
// }

foreach ($all as $group) {
    $count = count($group['items']);
    $label = $count === 1 ? $group['label'] : $group['label'] . 's';
    echo "Inserted $count $label" . PHP_EOL;
}
