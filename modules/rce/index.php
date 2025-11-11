<?php require_once __DIR__ . '/../../core/init.php'; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>远程代码执行（RCE）演示</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">远程代码执行（RCE）</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p class="muted">本页演示不安全的 <code>eval</code> 用法。输入任意 PHP 代码后端直接执行，属于典型 RCE。</p>
      <form action="/modules/rce/eval.php" method="get">
        <label for="code">输入要执行的 PHP 代码：</label>
        <input id="code" name="code" type="text" style="width:100%;margin-top:8px;" value="echo 'Hello from RCE';" />
        <div style="margin-top:12px;">
          <button class="btn btn-primary" type="submit">执行</button>
        </div>
      </form>
    </div>
    <div class="panel explain">
      <h3>提示</h3>
      <ul>
        <li>示例：<code>phpinfo();</code>、<code>echo 1+2;</code>、<code>system('whoami');</code></li>
        <li>修复方向：禁用动态执行（避免 eval），使用白名单与沙箱。</li>
      </ul>
    </div>
  </main>
</body>
</html>