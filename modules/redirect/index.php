<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
if (isset($_GET['to'])) {
    // 演示：开放重定向，未校验跳转目标
    header('Location: ' . $_GET['to']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>开放重定向</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">开放重定向</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <form method="get">
        <div class="field"><label>跳转到</label><input name="to" placeholder="https://evil.com/" /></div>
        <button class="btn btn-primary" type="submit">跳转</button>
      </form>
      <p class="muted">不做任何校验，容易被钓鱼/重定向劫持。</p>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>开放重定向指将用户可控的 URL 直接用于重定向，导致用户被引导到钓鱼或恶意站点。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>外部 URL：<code>?to=https://evil.com</code>。</li>
        <li>协议相对与双斜杠：<code>//evil.com</code>、反斜杠与编码绕过。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：仅允许站内路径，使用 ID 映射而非直接 URL。</li>
        <li>修复：解析并校验 host，拒绝跨域与协议相对地址。</li>
      </ul>
    </div>
  </main>
</body>
</html>