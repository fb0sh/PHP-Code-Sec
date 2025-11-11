<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
/*
 * 漏洞说明：二次解析（双重解码）
 * - 根因：网关/服务器对 URL 一次解码 + 应用层再次解码，危险序列（如 `../`）被还原，绕过路径与白名单校验。
 * - 复现：`file=..%252F..%252Fcore%252Finit.php` 经两次解码变为 `../../core/init.php` 并被拼接读取。
 * - 修复：统一并仅一次解码；路径规范化（realpath）与前缀校验；限制目录与固定文件名/扩展；避免将原始输入直接拼接路径。
 */
?>
<?php
// 演示：二次解析（双重解码）导致绕过校验与路径穿越
$fileParam = $_GET['file'] ?? '';
$decodedOnce = $fileParam ? rawurldecode($fileParam) : '';
$decodedTwice = $decodedOnce ? rawurldecode($decodedOnce) : '';
$base = __DIR__ . '/../../data/';
$output = '';
$err = '';

// 仅允许 data 目录，但双重解码后可能会变成 ../ 穿越
if ($decodedTwice) {
  $target = $base . $decodedTwice; // 漏洞：对参数进行二次解码后拼接路径
  if (is_file($target) && strpos(realpath($target), realpath($base)) === 0) {
    $output = @file_get_contents($target);
  } else {
    $err = '目标不存在或越界：' . h($target);
  }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>二次解析（双重解码）</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">二次解析</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p>此示例展示对参数进行二次解码可能绕过安全校验，造成路径穿越读取。</p>
      <form method="get">
        <div class="field"><label>file 参数</label><input name="file" placeholder="如: notes.txt 或注入：..%252F..%252Fcore%252Finit.php" /></div>
        <button class="btn btn-primary" type="submit">读取</button>
      </form>
      <p class="muted">一次解码：<code><?php echo h($decodedOnce); ?></code></p>
      <p class="muted">二次解码：<code><?php echo h($decodedTwice); ?></code></p>
    </div>
    <div class="panel">
      <?php if($err) echo '<p class="danger">'.$err.'</p>'; ?>
      <h3>读取结果</h3>
      <pre><?php echo h($output ?: '（空或失败）'); ?></pre>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>当网关/服务器对 URL 进行一次解码，而应用层又对参数进行二次解码时，原本被编码的危险序列（如 <code>../</code>）会被还原，绕过白名单或路径限制。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>双重解码绕过：提交 <code>..%252F..%252Fcore%252Finit.php</code>，经过两次解码变为 <code>../../core/init.php</code>。</li>
        <li>结合文件读取/包含：与 LFI 功能结合进一步扩大攻击面。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：统一解码策略，确保仅一次解码并在解码前后做规范化与白名单校验。</li>
        <li>修复：使用 <code>realpath</code> 与目录前缀校验，拒绝越界路径。</li>
      </ul>
    </div>
  </main>
</body>
</html>