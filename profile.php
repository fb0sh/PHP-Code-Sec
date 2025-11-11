<?php require_once __DIR__ . '/core/init.php'; ?>
<?php require_login(); $u = current_user(); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>个人中心 - PHP-Code-Sec</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav">
    <div class="brand">PHP<span class="accent">-Code</span>-Sec</div>
    <div class="links"><a href="/">首页</a> <a href="/logout.php">退出</a></div>
  </header>
  <main class="container">
    <div class="hero"><h1>个人中心</h1><p class="muted">这里可进入邮箱修改（CSRF演示）</p></div>
    <div class="panel">
      <p>用户ID：<?php echo h($u['id']); ?> / 用户名：<?php echo h($u['username']); ?> / 管理员：<?php echo h($u['is_admin']); ?></p>
      <p>跳转到：<a href="/modules/csrf/change_email.php">邮箱修改（CSRF）</a></p>
    </div>
  </main>
</body>
</html>