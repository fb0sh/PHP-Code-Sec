<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
// 演示：权限逻辑漏洞（参数控制）
$is_admin = (isset($_GET['admin']) && $_GET['admin'] === '1') ? true : false;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>权限逻辑绕过</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">权限逻辑</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p>通过 URL 参数 <code>?admin=1</code> 即可访问管理员区域：</p>
      <?php if($is_admin): ?>
        <div class="status">管理员区域：敏感信息展示</div>
        <pre>// 示例敏感信息
DB_HOST: <?php echo defined('DB_HOST') ? DB_HOST : '未安装'; ?>
DB_USER: <?php echo defined('DB_USER') ? DB_USER : '未安装'; ?>
        </pre>
      <?php else: ?>
        <p class="muted">当前非管理员。试试在地址后面加上 <code>?admin=1</code></p>
      <?php endif; ?>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>权限逻辑漏洞通常源于在客户端或 URL 参数中控制关键授权标志，服务端未进行真实身份与权限校验。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>参数控制：<code>?admin=1</code>、隐藏表单域等直接提升权限。</li>
        <li>信任前端：前端渲染的角色/价格等被篡改后服务端仍接受。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：所有权限判定在服务端完成，拒绝信任客户端传值。</li>
        <li>修复：按用户会话与角色进行鉴权与审计，最小权限。</li>
        <li>修复：关键操作二次确认与风控。</li>
      </ul>
    </div>
  </main>
</body>
</html>