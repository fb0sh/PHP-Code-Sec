<?php
// 安装向导：生成 config/config.inc.php 并初始化数据库

$installed = file_exists(__DIR__ . '/../config/config.inc.php');
// 新增：允许通过 force=1 强制重装；默认已安装显示提示但不重定向首页
$allow_reinstall = isset($_GET['force']) && $_GET['force'] === '1';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'] ?? '127.0.0.1';
    $port = $_POST['port'] ?? '3306';
    $user = $_POST['user'] ?? 'root';
    $pass = $_POST['pass'] ?? '';
    $db   = $_POST['db'] ?? 'phpsec_lab';

    try {
        $dsn = 'mysql:host=' . $host . ';port=' . $port . ';charset=utf8mb4';
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // 创建数据库
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        $pdo->exec("USE `{$db}`");

        // 创建表: users, comments
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE,
            password VARCHAR(255),
            email VARCHAR(255),
            is_admin TINYINT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            content TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // 初始数据
        $pdo->exec("INSERT IGNORE INTO users(username, password, email, is_admin) VALUES
            ('admin', MD5('admin123'), 'admin@example.com', 1),
            ('guest', MD5('guest'), 'guest@example.com', 0)");

        $pdo->exec("INSERT IGNORE INTO comments(user_id, content) VALUES
            (2, '欢迎来到 PHP-Code-Sec！试试注入与XSS吧~'),
            (1, '<b>管理员留言</b>: 请勿在生产环境部署该靶场。')");

        // 写配置文件
        if (!is_dir(__DIR__ . '/../config')) {
            mkdir(__DIR__ . '/../config', 0777, true);
        }
        $configPHP = "<?php\n".
            "define('DB_HOST', '" . addslashes($host) . "');\n".
            "define('DB_PORT', '" . addslashes($port) . "');\n".
            "define('DB_USER', '" . addslashes($user) . "');\n".
            "define('DB_PASS', '" . addslashes($pass) . "');\n".
            "define('DB_NAME', '" . addslashes($db) . "');\n";

        file_put_contents(__DIR__ . '/../config/config.inc.php', $configPHP);

        // 目录准备
        @mkdir(__DIR__ . '/../uploads', 0777, true);
        @mkdir(__DIR__ . '/../data', 0777, true);
        @mkdir(__DIR__ . '/../pages', 0777, true);

        $msg = '安装成功！已生成数据库与配置文件。<a href="/">返回首页</a>'; 
    } catch (Throwable $e) {
        $msg = '<span style="color:#ef4565">安装失败：' . htmlspecialchars($e->getMessage()) . '</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>安装向导 - PHP-Code-Sec</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav">
    <div class="brand">PHP<span class="accent">-Code</span>-Sec</div>
    <div class="links"><span class="status">安装向导</span></div>
  </header>
  <main class="container">
    <div class="hero">
      <h1>安装向导</h1>
      <p>填写 MySQL 信息并初始化数据库与配置。</p>
    </div>
    <div class="panel">
      <?php if (!empty($msg)) echo '<p>'.$msg.'</p>'; ?>

      <?php if (!$installed || $allow_reinstall): ?>
        <form method="post">
          <div class="grid">
            <div class="field"><label>主机</label><input name="host" value="127.0.0.1" /></div>
            <div class="field"><label>端口</label><input name="port" value="3306" /></div>
            <div class="field"><label>用户名</label><input name="user" value="root" /></div>
            <div class="field"><label>密码</label><input name="pass" value="" /></div>
            <div class="field"><label>数据库名</label><input name="db" value="phpsec_lab" /></div>
          </div>
          <div style="margin-top:12px">
            <button class="btn btn-primary" type="submit">开始安装</button>
          </div>
        </form>
        <?php if ($installed && $allow_reinstall): ?>
          <p class="muted" style="margin-top:12px">注意：当前为强制重装模式（force=1），将覆盖已存在配置。</p>
        <?php endif; ?>
      <?php else: ?>
        <p>检测到系统已安装并存在配置文件。</p>
        <p>如需重新安装或覆盖配置，请点击：<a class="btn" href="/setup/install.php?force=1">进入强制重装</a>。</p>
        <p style="margin-top:8px"><a href="/">返回首页</a></p>
      <?php endif; ?>

      <p class="muted" style="margin-top:12px">注意：本项目用于学习与审计演示，务必在隔离环境中使用。</p>
    </div>
  </main>
</body>
</html>