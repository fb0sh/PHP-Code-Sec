<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
/*
 * 漏洞说明：变量覆盖（extract）
 * - 根因：`extract($_GET)` 默认模式为 EXTR_OVERWRITE，会将请求参数键导入为变量并覆盖已有同名关键变量（如权限/角色）。
 * - 复现：`?is_admin=1&role=admin` 覆盖后端逻辑中的 `$is_admin/$role`，实现权限绕过。
 * - 修复：避免使用 `extract()`；改用白名单映射并进行类型与范围校验；关键变量不从请求派生。
 */
?>
<?php
// 演示：不安全的 extract() 导致变量覆盖与权限绕过
$role = 'user';
$is_admin = false;
$debug = [];

// 不安全做法：直接把 GET 参数提取到本地作用域（默认覆盖同名变量）
if (!empty($_GET)) {
    $debug['before'] = ['role' => $role, 'is_admin' => $is_admin];
    extract($_GET); // 漏洞：EXTR_OVERWRITE 覆盖同名关键变量
    $debug['after'] = ['role' => $role ?? null, 'is_admin' => $is_admin ?? null];
}

$message = $is_admin ? '你是管理员，可访问敏感操作' : '普通用户，仅限只读操作';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>变量覆盖（extract）漏洞</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">变量覆盖（extract）</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p>此示例通过 <code>extract($_GET)</code> 展示变量覆盖造成的权限逻辑绕过。</p>
      <form method="get">
        <div class="field"><label>is_admin</label><input name="is_admin" placeholder="如: 1 或 0" /></div>
        <div class="field"><label>role</label><input name="role" placeholder="如: admin 或 user" /></div>
        <button class="btn btn-primary" type="submit">提交</button>
      </form>
    </div>
    <div class="panel">
      <h3>当前状态</h3>
      <p>角色：<?php echo h($role); ?>；权限：<?php echo $is_admin ? '管理员' : '普通用户'; ?></p>
      <p class="muted">消息：<?php echo h($message); ?></p>
      <?php if($debug): ?>
        <pre><?php echo h(print_r($debug, true)); ?></pre>
      <?php endif; ?>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p><code>extract()</code> 会将数组键作为变量名导入当前作用域。默认模式 <code>EXTR_OVERWRITE</code> 会覆盖已有同名变量，导致攻击者通过参数伪造修改关键逻辑变量。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>权限绕过：访问 <code>?is_admin=1&amp;role=admin</code>，覆盖后端逻辑中的 <code>$is_admin</code> 与 <code>$role</code>。</li>
        <li>价格/配置篡改：覆盖 <code>$price</code>、<code>$config</code> 等关键变量。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>避免使用 <code>extract()</code>；改用白名单映射：<code>$role = $_GET['role'] ?? 'user';</code>。</li>
        <li>对关键变量使用不可覆盖的作用域或常量；集中从请求读取并校验。</li>
        <li>开启严格类型校验与参数验证（<code>filter_input</code>、枚举/常量、后端二次校验）。</li>
      </ul>
    </div>
  </main>
</body>
</html>