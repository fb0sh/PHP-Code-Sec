<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
/*
 * 漏洞说明：CSRF（跨站请求伪造）邮箱修改
 * - 根因：敏感操作通过 GET 请求直接生效，且缺失 CSRF Token 校验，允许第三方页面在用户不知情情况下发起状态改变。
 * - 复现：构造恶意页面嵌入 <img src="/modules/csrf/change_email.php?email=evil@evil.com">，登录用户访问即会触发修改。
 * - 影响：修改账户资料、指向攻击者邮箱、进一步劫持账户。
 * - 修复：仅接受 POST 并校验 CSRF Token；设置 Cookie SameSite；对来源（Referer/Origin）与关键操作做二次确认。
 */
?>
<?php require_login(); $pdo = db(); $msg = '';
if ($pdo && isset($_GET['email'])) {
    // 漏洞点：不校验 CSRF，且使用 GET 改变状态（高危）
    $email = $_GET['email'];
    $uid = current_user()['id'];
    try {
        $pdo->exec("UPDATE users SET email='".$email."' WHERE id=".$uid);
        $msg = '邮箱已更新为：' . h($email);
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
  <title>CSRF - 邮箱修改</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">CSRF 邮箱修改</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
      <form method="get">
        <div class="field"><label>新邮箱（GET参数）</label><input name="email" placeholder="example@domain.com" /></div>
        <button class="btn btn-primary" type="submit">修改邮箱</button>
      </form>
      <p class="muted">此处无任何 CSRF Token 校验，可被跨站请求伪造。</p>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>CSRF（跨站请求伪造）通过诱导已登录用户在不知情的情况下发起状态改变请求，如修改邮箱。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>隐藏表单或自动加载的图片/脚本标签发起 GET/POST。</li>
        <li>结合开放重定向或 <code>SameSite=None</code> 的 Cookie 传递。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：为重要操作加入 CSRF Token 并进行校验。</li>
        <li>修复：Cookie 设置 <code>SameSite=Lax/Strict</code>，关键操作使用 POST。</li>
        <li>修复：二次确认与仅接受站内来源（Referer/Origin）。</li>
      </ul>
    </div>
  </main>
</body>
</html>