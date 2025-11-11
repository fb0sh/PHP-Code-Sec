<?php require_once __DIR__ . '/../../core/init.php'; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>XSS - DOM 型</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">XSS DOM 型</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p>将 payload 放在 URL 的 <code>#hash</code> 中，例如：<code>#&lt;img src=x onerror=alert(1)&gt;</code></p>
      <div id="result" class="panel"></div>
    </div>
  </main>
  <div class="panel explain">
    <h3>原理解析</h3>
    <p>DOM 型 XSS 由前端在浏览器中不安全地处理可控数据（如 <code>location.hash</code>），导致把内容直接注入 DOM。</p>
    <h3>常见利用方式</h3>
    <ul>
      <li>把 payload 放在 hash/query，再由脚本写入 <code>innerHTML</code>。</li>
      <li>利用不安全的第三方前端组件或自定义解析。</li>
    </ul>
    <h3>常见绕过与修复</h3>
    <ul>
      <li>修复：使用 <code>textContent</code> 或安全模板渲染，不直接拼接为 HTML。</li>
      <li>修复：对 URL 片段进行严格校验与编码，结合 CSP。</li>
    </ul>
  </div>
  <script>
    // 演示：直接写入 hash 到 innerHTML，导致 DOM XSS
    document.getElementById('result').innerHTML = location.hash.slice(1);
  </script>
</body>
</html>