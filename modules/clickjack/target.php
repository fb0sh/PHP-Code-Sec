<?php require_once __DIR__ . '/../../core/init.php'; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>点击劫持目标页</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">点击劫持目标</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p class="muted">本页面未设置 X-Frame-Options 或 CSP 的 frame-ancestors，容易被第三方站点以 iframe 方式嵌入并覆盖点击区域。</p>
      <button id="transfer-btn" class="btn btn-primary" onclick="alert('已执行敏感操作（演示）');">确认转账 100 元（演示按钮）</button>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>点击劫持通过把目标页以 <code>iframe</code> 嵌入并用透明层覆盖，引导用户在不知情的情况下点击敏感按钮。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>覆盖按钮并伪装 UI，引导点击。</li>
        <li>在移动端利用尺寸与滚动造成误触。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：设置 <code>X-Frame-Options: DENY/SAMEORIGIN</code> 或 CSP 的 <code>frame-ancestors</code>。</li>
        <li>修复：关键操作采用双因子确认与可见性检测。</li>
      </ul>
    </div>
  </main>
</body>
</html>