<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
$token = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'] ?? 'guest';
    // 演示：弱令牌生成，使用 md5 + rand，不安全
    $token = md5($user . time() . rand());
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>弱加密与令牌</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">弱加密</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <form method="post">
        <div class="field"><label>用户名</label><input name="user" value="guest" /></div>
        <button class="btn btn-primary" type="submit">生成令牌</button>
      </form>
    </div>
    <?php if($token): ?>
    <div class="panel"><h3>令牌</h3><pre><?php echo h($token); ?></pre>
      <p class="muted">基于 md5 + rand 的令牌可预测，不安全。</p></div>
    <?php endif; ?>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>弱加密/令牌问题常见于使用过时算法（如 MD5/SHA1）、可预测随机数或不带认证的加密模式。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>令牌预测：时间戳 + 伪随机的组合可被枚举。</li>
        <li>ECB/CTR 无认证：可进行剪贴/重排或位翻转攻击。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：使用现代库（如 <code>libsodium</code> / OpenSSL <code>AES-GCM</code>）。</li>
        <li>修复：令牌使用 <code>random_bytes</code> 与 <code>bin2hex</code>，长度足够。</li>
        <li>修复：加密需同时认证（AEAD），避免仅哈希或仅加密。</li>
      </ul>
    </div>
  </main>
</body>
</html>