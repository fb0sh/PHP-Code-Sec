<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
// 演示：Host Header 注入，构造密码重置链接依赖 HTTP_HOST
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$reset = 'http://' . $host . '/reset.php?token=abc123';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Host Header 注入</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">Host Header</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p>当前构造的重置链接：</p>
      <p><code><?php echo h($reset); ?></code></p>
      <p class="muted">攻击者可通过伪造 Host 头注入恶意域名，通过邮件等渠道诱导用户。</p>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>当应用依赖 <code>HTTP_HOST</code> 构造绝对链接（如密码重置），若未校验 Host 头，攻击者可注入恶意域名。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>伪造 Host：通过代理或网关转发注入 <code>evil.com</code>。</li>
        <li>钓鱼链接：发送带恶意域名的重置/验证链接。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：使用配置的站点基准 URL，拒绝来自请求头的 Host。</li>
        <li>修复：解析并严格比对白名单域名与端口。</li>
      </ul>
    </div>
  </main>
</body>
</html>