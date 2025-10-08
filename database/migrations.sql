DROP DATABASE IF EXISTS `binsta`;

CREATE DATABASE `binsta`;

USE `binsta`;

CREATE TABLE `users` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) NULL,
    `bio` TEXT NULL,
    `pfp` VARCHAR(100) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `snippets` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `code` TEXT NOT NULL,
    `language` VARCHAR(50) NOT NULL,
    `caption` TEXT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);

CREATE TABLE `comments` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `snippet_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `comment` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`snippet_id`) REFERENCES `snippets` (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);

CREATE TABLE `likes` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `snippet_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    FOREIGN KEY (`snippet_id`) REFERENCES `snippets` (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);