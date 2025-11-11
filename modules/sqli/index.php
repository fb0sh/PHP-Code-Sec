<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
$pdo = db();
$res = null; $msg = '';
if ($pdo && isset($_GET['id'])) {
    // 数字型注入示例：id=1 或 id=1 OR 1=1
    $id = $_GET['id'];
    $sql = "SELECT id, username, email, is_admin FROM users WHERE id=" . $id;
    try { $res = $pdo->query($sql)->fetchAll(); } catch (Throwable $e) { $msg = h($e->getMessage()); }
}

if ($pdo && isset($_GET['q'])) {
    // 字符型注入示例：q=admin' OR '1'='1
    $q = $_GET['q'];
    $sql2 = "SELECT id, username, email, is_admin FROM users WHERE username='" . $q . "'";
    try { $res = $pdo->query($sql2)->fetchAll(); } catch (Throwable $e) { $msg = h($e->getMessage()); }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SQL 注入 - PHP-Code-Sec</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">SQL 注入</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <h3>数字型注入</h3>
      <form method="get">
        <div class="field"><label>用户ID</label><input name="id" placeholder="例如：1 或 1 OR 1=1" /></div>
        <button class="btn btn-primary" type="submit">查询</button>
      </form>
    </div>
    <div class="panel">
      <h3>字符型注入</h3>
      <form method="get">
        <div class="field"><label>用户名</label><input name="q" placeholder="例如：admin' OR '1'='1" /></div>
        <button class="btn btn-primary" type="submit">查询</button>
      </form>
    </div>
    <?php if($msg) echo '<p class="danger">'.$msg.'</p>'; ?>
    <?php if($res): ?>
      <div class="panel">
        <h3>查询结果</h3>
        <pre><?php echo h(print_r($res, true)); ?></pre>
      </div>
    <?php endif; ?>
    <div class="footer">示例为教学用途，禁止用于非法行为。</div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>SQL 注入发生在将未转义的输入拼接到 SQL 语句中，从而改变查询语义、读取或修改数据库。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>联合/错误注入：<code>UNION SELECT</code> 泄露数据，借助错误信息定位结构。</li>
        <li>布尔/时间盲注：<code>AND 1=1</code> 与 <code>SLEEP(5)</code> 等推断返回差异。</li>
        <li>绕过：编码、注释、大小写与函数包装规避过滤。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：使用预编译与参数化查询，不拼接字符串。</li>
        <li>修复：限制数据库权限、审计异常查询，关闭详细错误回显。</li>
        <li>修复：对输出进行最小化与转义，配合 WAF 与监控。</li>
      </ul>
    </div>
  </main>
</body>
</html>