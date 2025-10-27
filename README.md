# Binsta 📸

Binsta is a web application inspired by Instagram, designed for sharing code snippets. It allows users to create accounts, post code snippets, like and comment on posts, and more.

![License](https://img.shields.io/github/license/simpelcity/Binsta)
![GitHub issues](https://img.shields.io/github/issues/simpelcity/Binsta)
![GitHub pull requests](https://img.shields.io/github/issues-pr/simpelcity/Binsta)
![GitHub last commit](https://img.shields.io/github/last-commit/simpelcity/Binsta)

![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![CSS](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![HTML](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-323330?style=for-the-badge&logo=javascript&logoColor=F7DF1E)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Sass](https://img.shields.io/badge/Sass-CC6699?style=for-the-badge&logo=sass&logoColor=white)
![Twig](https://img.shields.io/badge/Twig-339933?style=for-the-badge&logo=twig&logoColor=white)

## 📋 Table of Contents

- [Binsta 📸](#binsta-)
  - [📋 Table of Contents](#-table-of-contents)
  - [About](#about)
  - [✨ Features](#-features)
  - [📦 Installation](#-installation)
    - [Option 1: Clone Repository](#option-1-clone-repository)
    - [Option 2: Download ZIP](#option-2-download-zip)
    - [Dependencies](#dependencies)
    - [Database](#database)
  - [⚙️ Configuration](#️-configuration)
    - [✏️ Code changes](#️-code-changes)
    - [XAMPP vhost](#xampp-vhost)
  - [🪲 Issues](#-issues)
  - [🤝 Contributing](#-contributing)
    - [Quick Contribution Steps](#quick-contribution-steps)
  
## About

Binsta is a full-stack social snippet sharing application, built to let developers and hobbyists publish, comment on, and like short code snippets across multiple languages. It combines modern PHP backend architecture, a Btrfs-based data layer, and a sleek Bootstrap-powered UI with Twig templating for clean and expressive rendering.

## ✨ Features

- 🎯 **Clean Design**: A visually appealing layout that focuses on your content.
- 🔐 **User Authentication**: Users can sign up and log in to manage their profiles and content.
- 📝 **Post Creation**: Share code snippets with syntax highlighting and formatting.
- ❤️ **Engagement**: Like and comment on posts to interact with the community.
- 📱 **Responsive Design**: Adapts seamlessly to different screen sizes and devices.
- 👥 **Followers & Following**: Follow other users and see their activity.

## 📦 Installation

### Option 1: Clone Repository

```bash
git clone git@git.nexed.com:0191bd26-8ad6-775a-a3fb-8161a33b6a11/0191d5d1-b9ce-7ad9-9c76-6deeabe8482d/Binsta-ecd4ba913395-445dbf0a9680.git
cd Binsta-ecd4ba913395-445dbf0a9680
```

### Option 2: Download ZIP

1.  Download the ZIP file from the Bitlab repository.
2.  Extract the contents to your desired location.
3.  Navigate to the project location.

### Dependencies

Install the necessary dependencies with

``` bash
composer i
npm i
```

### Database

Run the code in `migrations.sql` in your [phpmyadmin](http://localhost/phpmyadmin) to create the database, also run the `seeder.php` with :
```bash
php database/seeder.php
```

so that all data is inserted into the database.

## ⚙️ Configuration

### ✏️ Code changes

You have to change a couple of lines to make it work correctly,
`controllers/UserController.php`
  - lines `255` and `268` you have to insert your email address for the reset password function. 
  - line `256` you have to paste your google app password without spaces in between,
  - you need to have 2fa enabled on your google account to have access to app passwords. To make an app password, you have to go over to https://myaccount.google.com/apppasswords and create one with the name 'Mail' and paste the password without spaces on line `256`.


### XAMPP vhost

Add this to the end of your `httpd-vhost.conf` file

```conf
<VirtualHost *:80>
  ServerName binsta.nexed.com
  DocumentRoot "<PATH/TO/PROJECT>/public"
  <Directory "<PATH/TO/PROJECT>/public">
    Options +Indexes +Includes +FollowSymLinks +MultiViews
    AllowOverride All
    Require all granted
  </Directory>
</VirtualHost>
```

and restart your XAMPP modules.

## 🪲 Issues

1. Head over to the Bitlab repository.
2. Click on the Issues tab and then on "New issue".
3. Fill in what the issue is, and submit it.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit pull requests with improvements or bug fixes.

### Quick Contribution Steps

1.  🍴 Fork the repository
2.  🌟 Create your feature branch (`git checkout -b feature/AmazingFeature`)
3.  ✅ Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4.  📤 Push to the branch (`git push origin feature/AmazingFeature`)
5.  🔃 Open a Pull Request