<?php require_once __DIR__ . '/../../core/init.php'; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>XSS - 反射型</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">XSS 反射型</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <form method="get">
        <div class="field"><label>关键词</label><input name="q" placeholder="试试 &lt;script&gt;alert(1)&lt;/script&gt;" /></div>
        <button class="btn btn-primary" type="submit">搜索</button>
      </form>
    </div>
    <div class="panel">
      <h3>搜索结果</h3>
      <p>你搜索了：<?php echo isset($_GET['q']) ? ($_GET['q']) : '（空）'; ?></p>
      <p class="muted">此处未做任何过滤，易受反射型 XSS。</p>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>反射型 XSS 发生在将未过滤的输入直接回显到页面（搜索结果、错误信息），导致脚本在浏览器执行。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>直接注入：<code>&lt;script&gt;alert(1)&lt;/script&gt;</code>。</li>
        <li>事件处理与标签：<code>&lt;img onerror=alert(1) src=x&gt;</code>、<code>&lt;a href=javascript:alert(1)&gt;</code>。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：输出编码（HTML/属性/URL），避免把输入拼接成 HTML。</li>
        <li>修复：使用模板引擎安全转义，或白名单过滤允许的内容。</li>
        <li>修复：开启 CSP（Content Security Policy）限制脚本来源。</li>
      </ul>
    </div>
  </main>
</body>
</html>