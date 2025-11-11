<?php require_once __DIR__ . '/core/init.php'; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>密码重置 - 演示页</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">密码重置演示</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container">
    <div class="panel">
      <?php $token = $_GET['token'] ?? ''; ?>
      <p>收到的重置链接 Token：<code><?php echo h($token); ?></code></p>
      <p class="muted">此页面用于配合 Host Header 注入演示，实际系统中应对 token 做签名与有效期校验，并避免基于不可信的 Host 生成重置链接。</p>
    </div>
  </main>
</body>
</html>