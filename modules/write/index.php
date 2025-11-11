<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
// 演示：任意文件写入漏洞（危险示例）
// 直接将用户提供的路径与内容写入磁盘，无任何白名单/路径归一化/权限校验。

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $path = $_POST['path'] ?? '';
    $content = $_POST['content'] ?? '';
    $append = isset($_POST['append']);

    if ($path === '') {
        $message = '<span style="color:#ef4565">缺少写入路径</span>';
    } else {
        try {
            $flags = $append ? FILE_APPEND : 0;
            $bytes = file_put_contents($path, $content, $flags);
            if ($bytes === false) {
                $message = '<span style="color:#ef4565">写入失败</span>';
            } else {
                $message = '写入成功：<code>' . h($path) . '</code>（' . (int)$bytes . ' 字节）';
            }
        } catch (Throwable $e) {
            $message = '<span style="color:#ef4565">错误：' . h($e->getMessage()) . '</span>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>任意文件写入演示</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">任意文件写入</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p class="muted">本页演示未限制写入路径与内容，攻击者可写入任意位置，甚至覆盖代码或投递 WebShell。</p>
      <?php if (!empty($message)) echo '<p>'.$message.'</p>'; ?>
      <form method="post">
        <div class="field"><label>写入路径</label>
          <input name="path" placeholder="如: ../../webshell.php 或 ./uploads/demo.txt" style="width:100%" value="./uploads/demo.txt" />
        </div>
        <div class="field" style="margin-top:8px"><label>写入内容</label>
          <textarea name="content" rows="6" style="width:100%"><?php echo htmlspecialchars("<?php\n// 演示 WebShell（危险）：执行 GET 中的 cmd\nsystem(")?><?php echo '<?php'; ?> echo $_GET['cmd'] ?? ''; <?php echo '?>'; ?></textarea>
        </div>
        <div class="field" style="margin-top:8px">
          <label><input type="checkbox" name="append" /> 追加写入（不覆盖现有内容）</label>
        </div>
        <div style="margin-top:12px">
          <button class="btn btn-primary" type="submit">写入文件</button>
        </div>
      </form>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>未对用户可控的路径与内容进行任何限制或校验，允许越界目录与敏感文件覆盖，导致严重后果。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>上传 WebShell：路径 <code>../../webshell.php</code>，内容 <code>&lt;?php system($_GET['cmd']); ?&gt;</code>。</li>
        <li>覆盖配置/代码：写入到 <code>../config/config.inc.php</code> 或其他可执行文件。</li>
        <li>配合路径遍历：通过 <code>..</code> 穿越到目标目录，或写入日志/计划任务。</li>
      </ul>
      <h3>常见修复</h3>
      <ul>
        <li>仅允许固定目录白名单与受控文件名，拒绝 <code>..</code>、绝对路径与分隔符。</li>
        <li>归一化并校验目标路径前缀（如使用 <code>realpath</code> 与沙箱目录）。</li>
        <li>限制内容类型与长度，避免写入可执行代码；最小权限原则。</li>
      </ul>
    </div>
  </main>
</body>
</html>