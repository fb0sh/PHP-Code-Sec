<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
// 弱类型比较（魔法哈希 & 0e 特性）
// 使用 == 比较会触发字符串到数字的转换："0e....." 视为 0 的科学计数法，从而造成绕过

$msg = '';
$storedToken = '0e123456789'; // 演示用途的弱令牌
if (isset($_GET['token'])) {
    $t = $_GET['token'];
    if ($t == $storedToken) {
        $msg .= "使用 == 比较，token 验证通过（弱类型绕过）\n";
    } else {
        $msg .= "token 验证失败\n";
    }
}

$magicHash = '0e830400451993494058024219903391'; // md5('QNKCDZO') 的魔法哈希
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pwd = $_POST['pwd'] ?? '';
    $h = md5($pwd);
    if ($h == $magicHash) {
        $msg .= "密码哈希使用 == 比较，魔法哈希绕过成功：" . h($h) . "\n";
    } else {
        $msg .= "密码哈希验证失败：" . h($h) . "\n";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>弱类型比较（魔法哈希）</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">弱类型比较</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <h3>Token 弱比较</h3>
      <p class="muted">当后端用 <code>==</code> 比较字符串时，形如 <code>0e...</code> 的值会被当作数字 0 比较，导致绕过。</p>
      <form method="get">
        <div class="field"><label>token</label><input name="token" placeholder="例如：0e999999999" /></div>
        <button class="btn btn-primary" type="submit">校验</button>
      </form>
    </div>
    <div class="panel">
      <h3>魔法哈希绕过</h3>
      <p class="muted">示例：<code>md5('QNKCDZO')</code> 为 <code><?php echo h($magicHash); ?></code>，与其它 <code>0e...</code> 通过 <code>==</code> 比较时可能绕过。</p>
      <form method="post">
        <div class="field"><label>密码</label><input name="pwd" placeholder="试试 QNKCDZO" /></div>
        <button class="btn btn-primary" type="submit">提交</button>
      </form>
    </div>
    <?php if($msg) echo '<pre>'.h($msg).'</pre>'; ?>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>弱类型比较在使用 <code>==</code> 时会发生类型转换，形如 <code>0e...</code> 的字符串可被视为 0 的科学计数法，从而绕过校验。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>魔法哈希：特定字符串的哈希值形如 <code>0e...</code>，与其它 <code>0e...</code> 在 <code>==</code> 下相等。</li>
        <li>令牌/验证码：使用 <code>==</code> 比较导致任意通过。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：使用严格比较 <code>===</code>，避免隐式类型转换。</li>
        <li>修复：对哈希值按字符串比较，并对输入做类型校验。</li>
      </ul>
    </div>
  </main>
</body>
</html>