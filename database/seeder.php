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

R::exec('SET FOREIGN_KEY_CHECKS = 0;');

R::wipe('comments');
R::wipe('likes');
R::wipe('snippets');
R::wipe('users');

R::exec('SET FOREIGN_KEY_CHECKS = 1;');

// --- USERS ---
$users = [
    [
        'username' => 'Simpelcity',
        'email' => 'simpelcity@example.com',
        'password' => password_hash('admin', PASSWORD_BCRYPT),
    ],
    [
        'username' => 'future-tech-leader',
        'email' => 'future-tech-leader@example.com',
        'password' => password_hash('bit_academy', PASSWORD_BCRYPT),
    ],
    [
        'username' => 'Vinxy',
        'email' => 'vinxy@example.com',
        'password' => password_hash('vinxy_user', PASSWORD_BCRYPT),
    ]
];

$userBeans = [];
foreach ($users as $data) {
    $user = R::dispense('users');
    $user->username = $data['username'];
    $user->email = $data['email'];
    $user->password = $data['password'];
    R::store($user);
    $userBeans[$data['username']] = $user;
}

// --- SNIPPETS ---

$snippetContent1 = <<<'PHP'
<?php

namespace Binsta\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use Binsta\Models\User;
use Binsta\Models\Snippet;

class SnippetController extends BaseController
{
    public function create()
    {
        $this->authorizeUser();
        $user = User::findById($_SESSION['user']);
        $userProfile = User::findById($_SESSION['user']);

        renderPage('snippets/create.twig', [
            'title' => 'Create snippet',
            'activeController' => 'create',
            'user' => $user,
            'userProfile' => $userProfile,
        ]);
    }

    public function createPost()
    {
        if (
            empty($_POST['code']) ||
            empty($_POST['language']) ||
            empty($_POST['caption'])
        ) {
            renderPage('snippets/create.twig', [
                'title' => 'Create snippet',
                'activeController' => 'create',
                'error' => 'Please fill in all fields',
            ]);
        }

        Snippet::create($_POST['code'], $_POST['language'], $_POST['caption'], $_SESSION['user'], new \DateTime());
        header('Location: /');
        exit;
    }
}
PHP;

$snippetContent2 = <<<'PHP'
<?php

namespace Binsta\Models;

use RedBeanPHP\R as R;

class Comment
{
    public static function create($content, $userId, $snippetId, $createdAt)
    {
        $comment = R::dispense('comments');
        $comment->comment = $content;
        $comment->user_id = $userId;
        $comment->snippet_id = $snippetId;
        $comment->created_at = $createdAt;
        return R::store($comment);
    }

    public static function findBySnippetId($snippetId, $limit, $offset)
    {
        $comments = R::findAll('comments', 'snippet_id = ? ORDER BY created_at ASC LIMIT ? OFFSET ?', [$snippetId, $limit, $offset]);

        foreach ($comments as $comment) {
            $comment->author = User::findById($comment->user_id);
        }

        return $comments;
    }

    public static function countBySnippetId($snippetId)
    {
        return R::count('comments', 'snippet_id = ?', [$snippetId]);
    }
}
PHP;

$snippetContent3 = <<<'JS'
document.addEventListener("DOMContentLoaded", () => {
	const languageSelect = document.getElementById("language-select");

	let languages = [
		{ name: "HTML", value: "html" },
		{ name: "CSS", value: "css" },
		{ name: "C-like", value: "clike" },
		{ name: "JavaScript", value: "javascript" },
		{ name: "C", value: "c" },
		{ name: "C#", value: "csharp" },
		{ name: "C++", value: "cpp" },
		{ name: "JSON", value: "json" },
		{ name: "Markdown", value: "markdown" },
		{ name: "PHP", value: "php" },
		{ name: "Python", value: "python" },
		{ name: "React JSX", value: "jsx" },
		{ name: "React TSX", value: "tsx" },
		{ name: "Sass", value: "scss" },
		{ name: "SQL", value: "sql" },
		{ name: "Twig", value: "twig " },
		{ name: "TypeScript", value: "typescript" },
	];

	languages.forEach((lang) => {
		languageSelect.innerHTML += `
		<option value="${lang.value}">${lang.name}</option>
		`;
	});
});
JS;

$snippetContent4 = <<<'TWIG'
{% extends '/layouts/app.twig' %} {% block content %}
<h1 class="my-4">Search Results for "{{ searchTerm }}"</h1>

{% if users|length > 0 %} {% for user in users %}
<div class="card my-2" id="user-{{ user.id }}">
	<div class="d-flex p-3 align-items-center">
		<a href="/user/profile/{{ user.id }}" class="d-flex align-items-center text-decoration-none text-black">
			{% if user.pfp %}
			<img src="/assets/uploads/{{ user.pfp }}" alt="User" class="nav-icon me-2 rounded-circle" />
			{% else %}
			<img src="/assets/icons/light/profile-user.png" alt="User" class="nav-icon me-2 theme-icon" />
			{% endif %}
			<p class="m-0 fw-bold">{{ user.username }}</p>
		</a>
	</div>
</div>
{% endfor %} {% else %}
<p>No results found for "{{ searchTerm }}".</p>
{% endif %} {% endblock %}
TWIG;

$snippetContent5 = <<<'SQL'
DROP DATABASE IF EXISTS `bobby_tables`;

CREATE DATABASE `bobby_tables`;

USE `bobby_tables`;

CREATE TABLE `users` (
    id int AUTO_INCREMENT PRIMARY KEY,
    username varchar(100),
    password varchar(100)
);

INSERT INTO users (`username`, `password`) values ('bobby', 'password');
SQL;

$snippetContent6 = <<<'SCSS'
@media (max-width: 992px) {
	[data-bs-theme="light"] {
		.links > div > a,
		.dropdown-group {
			background-color: rgba(0, 0, 0, 0.05);
			color: black;
			border-radius: 0.375rem;
			padding: 0.75rem;
		}

		.links > div > a:hover,
		.dropdown-group:hover {
			background-color: rgba(0, 0, 0, 0.1);
		}
	}

	[data-bs-theme="dark"] {
		.links > div > a,
		.dropdown-group {
			background-color: rgba(255, 255, 255, 0.1);
			color: white;
			border-radius: 0.375rem;
			padding: 0.75rem;
		}

		.links > div > a:hover,
		.dropdown-group:hover {
			background-color: rgba(255, 255, 255, 0.2);
		}
	}
}
SCSS;

$snippetContent7 = <<<'JS'
const { Discord, Client, GatewayIntentBits } = require("discord.js");
const config = require("./config.json");

const client = new Client({
    intents: [
        GatewayIntentBits.Guilds,
        GatewayIntentBits.GuildMessages,
        GatewayIntentBits.MessageContent,
    ]
});
const webhookClient = new Discord.WebhookClient(config.webhookID, config.webhookToken);

client.on("message", (message) => {
    if (!message.member.roles.cache.has(config["announcer-role"]) || !message.content.startsWith("!") || message.author.bot) return;

    const args = message.content.slice(1).trim().split(' ');
    const command = args.shift().toLowerCase();

    if (command == "announce") {
        var announcement = "";
        for (const word in args) {
            announcement = announcement + args[word] + " ";
        }
        webhookClient.send(announcement)
    }
})

client.login(config.token);
JS;

$snippets = [
    [
        'user' => 'Simpelcity',
        'code' => $snippetContent1,
        'language' => 'php',
        'caption' => 'Binsta SnippetController',
    ],
    [
        'user' => 'Simpelcity',
        'code' => $snippetContent2,
        'language' => 'php',
        'caption' => 'Binsta Comment model',
    ],
    [
        'user' => 'future-tech-leader',
        'code' => $snippetContent3,
        'language' => 'javascript',
        'caption' => 'Binsta snippet.js',
    ],
    [
        'user' => 'Vinxy',
        'code' => $snippetContent4,
        'language' => 'twig',
        'caption' => 'Binsta search template',
    ],
    [
        'user' => 'future-tech-leader',
        'code' => $snippetContent5,
        'language' => 'sql',
        'caption' => 'SQL code for netland database',
    ],
    [
        'user' => 'Vinxy',
        'code' => $snippetContent6,
        'language' => 'scss',
        'caption' => 'Binsta mobile navbar styling'
    ],
    [
        'user' => 'Simpelcity',
        'code' => $snippetContent7,
        'language' => 'javascript',
        'caption' => 'Announcement discord bot',
    ]
];

date_default_timezone_set('Europe/Amsterdam');

$snippetBeans = [];
$time = new DateTime();
foreach ($snippets as $index => $data) {
    $snippet = R::dispense('snippets');
    $snippet->user_id = $userBeans[$data['user']]->id;
    $snippet->code = $data['code'];
    $snippet->language = $data['language'];
    $snippet->caption = $data['caption'];

    $snippetTime = clone $time;
    $snippetTime->modify("-$index minutes");
    $snippet->created_at = $snippetTime->format('Y-m-d H:i:s');
    R::store($snippet);
    $snippetBeans[] = $snippet;
}

// --- COMMENTS ---

$comments = [
    [
        'snippet' => 0,
        'user' => 'Vinxy',
        'comment' => 'Crazy code snippet',
    ],
    [
        'snippet' => 6,
        'user' => 'future-tech-leader',
        'comment' => 'Nice discord bot',
    ],
    [
        'snippet' => 3,
        'user' => 'Simpelcity',
        'comment' => 'Is that Bootstrap?',
    ],
    [
        'snippet' => 0,
        'user' => 'future-tech-leader',
        'comment' => 'Nice code structure',
    ]
];

foreach ($comments as $data) {
    $comment = R::dispense('comments');
    $comment->snippet_id = $snippetBeans[$data['snippet']]->id;
    $comment->user_id = $userBeans[$data['user']]->id;
    $comment->comment = $data['comment'];
    $comment->created_at = date('Y-m-d H:i:s');
    R::store($comment);
}

$likes = [
    ['snippet' => 0, 'user' => 'Vinxy'],
    ['snippet' => 0, 'user' => 'future-tech-leader'],
    ['snippet' => 2, 'user' => 'Simpelcity'],
    ['snippet' => 6, 'user' => 'future-tech-leader'],
    ['snippet' => 5, 'user' => 'Simpelcity'],
];

foreach ($likes as $data) {
    $like = R::dispense('likes');
    $like->snippet_id = $snippetBeans[$data['snippet']]->id;
    $like->user_id = $userBeans[$data['user']]->id;
    R::store($like);
}

$all = [
    ['type' => 'users', 'items' => $users, 'label' => 'user'],
    ['type' => 'snippets', 'items' => $snippets, 'label' => 'snippet'],
    ['type' => 'comments', 'items' => $comments, 'label' => 'comment'],
    ['type' => 'likes', 'items' => $likes, 'label' => 'like'],
];

foreach ($all as $group) {
    echo "Wiped table " . $group['type'] . PHP_EOL;
    $count = count($group['items']);
    $label = $count === 1 ? $group['label'] : $group['label'] . 's';
    echo "Inserted $count $label" . PHP_EOL;
}
