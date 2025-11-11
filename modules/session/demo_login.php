<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
$msg='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 演示：登录后不 regenerate 会话ID，存在固定风险
    $_SESSION['user_id'] = 999;
    $_SESSION['username'] = 'demo';
    $msg = '已登录为 demo（未 regenerate 会话ID）';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>会话固定示例</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">会话固定</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
      <form method="post">
        <button class="btn btn-primary" type="submit">模拟登录</button>
      </form>
      <p class="muted">登录后未调用 <code>session_regenerate_id(true)</code>，可能被攻击者固定会话。</p>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>会话固定利用在登录前先设置会话 ID，若登录后不 regenerate，会沿用旧 ID 导致被劫持。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>攻击者在受害者访问前设置固定的 <code>PHPSESSID</code>。</li>
        <li>登录后会话未更新，攻击者复用该会话访问。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：登录/提权后调用 <code>session_regenerate_id(true)</code>。</li>
        <li>修复：设置 Cookie 安全属性与生命周期，结合绑定设备/IP 风控。</li>
      </ul>
    </div>
  </main>
</body>
</html>