<?php
// 基础初始化
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_PATH', __DIR__ . '/..');
define('APP_INSTALLED', file_exists(APP_PATH . '/config/config.inc.php'));

// 如果未安装且不在安装页，则跳转
$reqPath = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
if (!APP_INSTALLED && strpos($reqPath, '/setup/install.php') === false) {
    header('Location: /setup/install.php');
    exit;
}

// 加载配置（如果已安装）
if (APP_INSTALLED) {
    require_once APP_PATH . '/config/config.inc.php';
}

require_once APP_PATH . '/core/db.php';

function current_user() {
    if (!empty($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? 'unknown',
            'is_admin' => $_SESSION['is_admin'] ?? 0,
        ];
    }
    return null;
}

function require_login() {
    if (!current_user()) {
        header('Location: /login.php');
        exit;
    }
}

function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

?>