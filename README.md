# Binsta

## About binsta

Binsta is a form of [Instagram](https://instagram.com) where you can share code snippets. Users can create an account, create and share posts, like posts and comment on posts, and much more. Below you can read how to install binsta on your device to test it.

## Installation

### Dependencies

Install the necessary dependencies with

``` bash
composer i
npm i
```

### Database

Run the code in migrations.sql in phpmyadmin to create the database, also run the seeder.php so that all data is inserted into the database.

### Code changes

You have to change a couple of lines to make it work correctly,
/controllers/UserController.php
- line 171 you have to paste the path to the project,
- lines 255 and 268 you have to insert your email address for the reset password function. 
- line 256 you have to paste your google app password without spaces in between,
- you need to have 2fa enabled on your google account to have access to app passwords. To make an app password, you have to go over to https://myaccount.google.com/apppasswords and create one with the name 'Mail' and paste the password without spaces on line 256.

### Notes

Change the DocumentRoot and Directory in "httpd.conf" or "/extra/httpd-vhost.conf" to the /public folder, and restart your XAMPP modules. If you're on Linux, you have to give apache access and read/write permission to the uploads folder, to do this run these commands in your terminal

```bash
sudo chown -R daemon:daemon /path/to/project/public/assets/uploads  # Change owner to Apache
chmod 755 path/to/project/public/assets/uploads                     # Make readable/writable
```

to change permissions back to your user, run this command
```bash
sudo chown -R $USER:$USER /path/to/project/public/assets/uploads
```

example project path to uploads folder
```bash
/home/simpelcity/Documents/nexed/Full-stack/Your-Fullstack-Framework/06-level-project/Binsta-ecd4ba913395-445dbf0a9680/public/assets/uploads
```