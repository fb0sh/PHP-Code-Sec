<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
/*
 * 漏洞说明：LDAP 注入
 * - 根因：将输入直接拼接到 LDAP 过滤器（如 `(uid=$u)`），攻击者可通过特殊语法扩展检索范围，返回管理员等敏感条目。
 * - 复现：`u=*)(|(role=admin))(`，模拟结果显示管理员记录。
 * - 修复：严格白名单输入、拒绝特殊符；使用参数化接口/统一转义；结果集再按权限过滤。
 */
?>
<?php
// 演示：将用户输入拼接到 LDAP 过滤器导致结果扩展（LDAP 注入）
$users = [
  ['uid' => 'alice', 'role' => 'user', 'secret' => 'alice-note-123'],
  ['uid' => 'bob', 'role' => 'admin', 'secret' => 'flag-admin-bob'],
  ['uid' => 'carol', 'role' => 'user', 'secret' => 'carol-note-xyz'],
];

$u = $_GET['u'] ?? '';
$filter = "(&(objectClass=person)(|(uid=$u)(mail=$u@example.com)))"; // 漏洞：直接拼接到过滤器
$result = [];

if ($u !== '') {
  // 仅为演示，模拟过滤器匹配：如果过滤器包含 role=admin 片段，则返回管理员记录（模拟注入扩展结果）
  if (strpos($filter, "role=admin") !== false) {
    foreach ($users as $row) if ($row['role'] === 'admin') $result[] = $row;
  } else {
    foreach ($users as $row) if ($row['uid'] === $u) $result[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>LDAP 注入</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">LDAP 注入</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p>示例以数组模拟目录服务；通过将输入直接拼接进 LDAP 过滤器，注入可扩展检索结果（如包含管理员记录）。</p>
      <form method="get">
        <div class="field"><label>用户名</label><input name="u" placeholder="如: alice 或注入：*)(|(role=admin))(" /></div>
        <button class="btn btn-primary" type="submit">检索</button>
      </form>
    </div>
    <div class="panel">
      <h3>过滤器</h3>
      <p class="muted"><code><?php echo h($filter); ?></code></p>
      <h3>查询结果</h3>
      <?php if($result): ?>
        <ul>
          <?php foreach ($result as $row): ?>
            <li>uid=<code><?php echo h($row['uid']); ?></code>, role=<code><?php echo h($row['role']); ?></code>, secret=<code><?php echo h($row['secret']); ?></code></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>（无结果或未检索）</p>
      <?php endif; ?>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>LDAP 注入发生在将用户输入直接拼接到过滤器（如 <code>(uid=$u)</code>）时，攻击者可构造特殊语法扩展检索范围，获取更敏感的条目。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>条件扩展：<code>u=*)(|(role=admin)) (</code> 使结果包含管理员记录。</li>
        <li>多属性匹配：构造 <code>(|(uid=...)(mail=...))</code> 绕过仅按 uid 的限制。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：严格白名单输入，拒绝 <code>* ( ) | &amp;</code> 等特殊符号。</li>
        <li>修复：使用安全 API/参数化接口构建过滤器；统一编码转义。</li>
        <li>修复：后端再做权限过滤，避免仅靠目录查询控制访问。</li>
      </ul>
    </div>
  </main>
</body>
</html>