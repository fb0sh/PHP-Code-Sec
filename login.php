<?php require_once __DIR__ . '/core/init.php'; ?>
<?php
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';

    // 演示：易受SQL注入影响（拼接查询语句），弱加密MD5
    $pdo = db();
    if ($pdo) {
        $sql = "SELECT * FROM users WHERE username='".$u."' AND password=MD5('".$p."') LIMIT 1";
        try {
            $row = $pdo->query($sql)->fetch();
            if ($row) {
                // 故意不调用 session_regenerate_id(true) 演示会话固定
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['is_admin'] = $row['is_admin'];
                header('Location: /');
                exit;
            } else {
                $msg = '<span class="danger">登录失败</span>';
            }
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
  <title>登录 - PHP-Code-Sec</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav">
    <div class="brand">PHP<span class="accent">-Code</span>-Sec</div>
    <div class="links"><a href="/register.php">注册</a></div>
  </header>
  <main class="container">
    <div class="hero"><h1>登录</h1><p class="muted">演示弱口令与注入绕过</p></div>
    <div class="panel">
      <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
      <form method="post">
        <div class="field"><label>用户名</label><input name="username" /></div>
        <div class="field"><label>密码</label><input name="password" type="password" /></div>
        <button class="btn btn-primary" type="submit">登录</button>
      </form>
      <p class="muted" style="margin-top:12px">示例注入：用户名：admin 密码：' OR '1'='1</p>
    </div>
  </main>
</body>
</html>