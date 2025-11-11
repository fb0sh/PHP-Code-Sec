<?php require_once __DIR__ . '/core/init.php'; ?>
<?php
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $e = $_POST['email'] ?? '';
    $pdo = db();
    if ($pdo) {
        // 演示：弱加密 MD5、无复杂校验、可被SQL注入
        $sql = "INSERT INTO users(username, password, email, is_admin) VALUES('".$u."', MD5('".$p."'), '".$e."', 0)";
        try {
            $pdo->exec($sql);
            $msg = '注册成功，<a href="/login.php">去登录</a>';
        } catch (Throwable $e) {
            $msg = '<span class="danger">错误：'.h($e->getMessage()).'</span>';
        }
    } else {
        $msg = '<span class="danger">数据库未连接，请先安装</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>注册 - PHP-Code-Sec</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav">
    <div class="brand">PHP<span class="accent">-Code</span>-Sec</div>
    <div class="links"><a href="/login.php">登录</a></div>
  </header>
  <main class="container">
    <div class="hero"><h1>注册</h1><p class="muted">示范弱加密与注入风险</p></div>
    <div class="panel">
      <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
      <form method="post">
        <div class="field"><label>用户名</label><input name="username" /></div>
        <div class="field"><label>密码</label><input name="password" type="password" /></div>
        <div class="field"><label>邮箱</label><input name="email" /></div>
        <button class="btn btn-primary" type="submit">注册</button>
      </form>
    </div>
  </main>
</body>
</html>