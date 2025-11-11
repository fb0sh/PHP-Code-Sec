<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
$msg = '';$content = '';
if (isset($_GET['file'])) {
    // 演示：未限制目录，存在路径遍历读取
    $file = $_GET['file'];
    $target = __DIR__ . '/../../data/' . $file;
    if (is_file($target)) {
        $content = file_get_contents($target);
    } else {
        $msg = '<span class="danger">文件不存在：'.h($target).'</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>路径遍历</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">路径遍历</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
      <form method="get">
        <div class="field"><label>文件名</label><input name="file" placeholder="例如：notes.txt 或 ../core/init.php" /></div>
        <button class="btn btn-primary" type="submit">读取</button>
      </form>
    </div>
    <?php if($content): ?>
    <div class="panel"><h3>内容</h3><pre><?php echo h($content); ?></pre></div>
    <?php endif; ?>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>路径遍历发生在将用户提供的文件名拼接到服务器路径时，攻击者通过 <code>../</code> 等序列访问越权文件。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>相对路径：<code>../../etc/passwd</code>、<code>..\\..\\Windows\\win.ini</code>。</li>
        <li>编码绕过：<code>%2e%2e/</code>、重复分隔符、绝对路径覆盖。</li>
        <li>包装器读取：<code>php://filter</code> 查看源码，<code>data://</code> 注入内容。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：对路径进行归一化与白名单校验，仅允许固定目录内的文件名。</li>
        <li>修复：禁止绝对路径与上级目录，移除路径分隔符与特殊字符。</li>
        <li>修复：隔离敏感文件与下载目录，避免代码与数据混放。</li>
      </ul>
    </div>
  </main>
</body>
</html>