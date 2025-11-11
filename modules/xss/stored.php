<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
$pdo = db();
$msg = '';
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? '';
    $uid = current_user()['id'] ?? 2; // 未登录则默认 guest
    try {
        $pdo->exec("INSERT INTO comments(user_id, content) VALUES({$uid}, '".$content."')");
        $msg = '已提交（未过滤，存在存储型XSS风险）';
    } catch (Throwable $e) {
        $msg = '<span class="danger">错误：'.h($e->getMessage()).'</span>';
    }
}
$rows = $pdo ? $pdo->query('SELECT c.id, u.username, c.content, c.created_at FROM comments c LEFT JOIN users u ON c.user_id=u.id ORDER BY c.id DESC LIMIT 20')->fetchAll() : [];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>XSS - 存储型</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">XSS 存储型</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
      <form method="post">
        <div class="field"><label>留言内容</label><textarea name="content" rows="3" placeholder="支持HTML，未过滤"></textarea></div>
        <button class="btn btn-primary" type="submit">提交</button>
      </form>
    </div>
    <div class="panel">
      <h3>最新评论</h3>
      <?php foreach($rows as $r): ?>
        <div style="padding:8px;border-bottom:1px dashed rgba(127,90,240,0.2)">
          <strong><?php echo h($r['username'] ?: 'anonymous'); ?></strong> 评论：
          <div><?php echo $r['content']; ?></div>
          <small class="muted">时间：<?php echo h($r['created_at']); ?></small>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>存储型 XSS 将恶意内容写入数据库或文件，随后被其他用户页面加载并执行。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>评论/昵称字段注入脚本，在列表页执行。</li>
        <li>富文本/Markdown 解析器的白名单缺陷。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：对输入进行白名单过滤，输出时进行正确的编码。</li>
        <li>修复：独立存储和渲染富文本，使用安全渲染库。</li>
        <li>修复：配合 CSP 与 HttpOnly/SameSite Cookie 缓解影响。</li>
      </ul>
    </div>
  </main>
</body>
</html>