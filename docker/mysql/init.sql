-- MySQL初始化脚本
-- 为PHP-Code-Sec靶场创建数据库和表结构

-- 创建数据库（如果不存在）
CREATE DATABASE IF NOT EXISTS `phpsec_lab` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `phpsec_lab`;

-- 用户表
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE,
    `password` VARCHAR(255),
    `email` VARCHAR(255),
    `is_admin` TINYINT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 评论表
CREATE TABLE IF NOT EXISTS `comments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `content` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 插入初始用户数据
INSERT INTO `users` (`username`, `password`, `email`, `is_admin`) VALUES
    ('admin', MD5('admin123'), 'admin@example.com', 1),
    ('guest', MD5('guest'), 'guest@example.com', 0)
ON DUPLICATE KEY UPDATE `username` = `username`;

-- 插入初始评论数据
INSERT INTO `comments` (`user_id`, `content`) VALUES
    (2, '欢迎来到 PHP-Code-Sec！试试注入与XSS吧~'),
    (1, '<b>管理员留言</b>: 请勿在生产环境部署该靶场。')
ON DUPLICATE KEY UPDATE `user_id` = `user_id`;
