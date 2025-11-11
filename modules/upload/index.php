<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $f = $_FILES['file'];
    if ($f['error'] === UPLOAD_ERR_OK) {
        // 演示：仅基于文件名扩展名做简单判断，可双扩展绕过，如 shell.php.jpg
        $name = $f['name'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allow = ['jpg','png','gif','jpeg'];
        if (in_array($ext, $allow)) {
            $target = __DIR__ . '/../../uploads/' . basename($name);
            if (move_uploaded_file($f['tmp_name'], $target)) {
                $msg = '上传成功：<a href="/uploads/'.h(basename($name)).'" target="_blank">查看文件</a>';
            } else {
                $msg = '<span class="danger">移动失败</span>';
            }
        } else {
            $msg = '<span class="danger">不允许的扩展名：'.h($ext).'</span>';
        }
    } else {
        $msg = '<span class="danger">上传错误代码：'.$f['error'].'</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>文件上传绕过</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">文件上传</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
      <form method="post" enctype="multipart/form-data">
        <div class="field"><label>选择文件</label><input type="file" name="file" /></div>
        <button class="btn btn-primary" type="submit">上传</button>
      </form>
      <p class="muted">仅扩展名校验，易受双扩展绕过。未校验 MIME 与内容。</p>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>文件上传绕过通常源于对扩展名或 MIME 的不严谨校验，导致可上传可执行脚本或包含恶意内容。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>双扩展与大小写：<code>shell.php.jpg</code>、<code>pHp</code>、<code>phtml</code>。</li>
        <li>MIME 混淆与内容欺骗：前端可控的 <code>Content-Type</code> 与伪造文件头。</li>
        <li>路径与覆盖：可控文件名导致目录穿越或覆盖既有文件。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：后端强制白名单（扩展与 MIME）并检测内容魔数。</li>
        <li>修复：随机化文件名与隔离存储，禁止在 Web 根目录下可执行。</li>
        <li>修复：对上传目录设置不可执行，必要时进行图片重采样与安全处理。</li>
      </ul>
    </div>
  </main>
</body>
</html>