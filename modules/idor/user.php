<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php require_login(); $pdo = db(); $msg=''; $user=null;
// 演示：仅依赖URL参数 id，未校验是否为当前用户，造成越权访问（IDOR）
if ($pdo && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $user = $pdo->query("SELECT id, username, email, is_admin, created_at FROM users WHERE id=".$id)->fetch();
        if (!$user) $msg = '<span class="danger">未找到用户</span>';
    } catch (Throwable $e) {
        $msg = '<span class="danger">错误：'.h($e->getMessage()).'</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>IDOR 越权读取</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">IDOR 越权</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <form method="get">
        <div class="field"><label>用户ID</label><input name="id" placeholder="例如：1 或 2" /></div>
        <button class="btn btn-primary" type="submit">查看</button>
      </form>
      <p class="muted">此接口未校验访问者是否有权查看该ID的用户信息。</p>
    </div>
    <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
    <?php if($user): ?>
    <div class="panel"><h3>用户信息</h3>
      <pre><?php echo h(print_r($user, true)); ?></pre>
    </div>
    <?php endif; ?>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>IDOR（不安全的直接对象引用）通过猜测或指定对象标识（如用户ID）访问未授权的资源。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>遍历 ID：<code>?id=1</code>、<code>?id=2</code> 查看他人信息。</li>
        <li>结合筛选与导出接口批量获取数据。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：服务端基于会话与权限校验对象所有权。</li>
        <li>修复：对敏感接口进行访问控制与速率限制。</li>
      </ul>
    </div>
  </main>
</body>
</html>