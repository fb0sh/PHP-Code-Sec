<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
/*
 * 漏洞说明：PHP 流包装器信息泄露
 * - 根因：文件读取功能未限制路径与包装器前缀，`php://filter/convert.base64-encode/resource=` 可将源码以 Base64 输出，泄露敏感信息。
 * - 复现：读取 `index.php`/`core/init.php` 等文件，观察源码被回显。
 * - 修复：路径白名单与扩展限制；拒绝 `php://`、`phar://`、`data://`；关闭错误与目录索引，降低攻击面。
 */
?>
<?php
// 演示：允许 php://filter 等流包装器导致源码信息泄露
$path = $_GET['path'] ?? 'index.php';
$content = '';$msg='';

if ($path) {
    $wrapper = 'php://filter/convert.base64-encode/resource=' . $path; // 漏洞：未限制包装器与路径
    $data = @file_get_contents($wrapper);
    if ($data !== false) {
        $content = base64_decode($data);
    } else {
        $msg = '读取失败：请确认文件路径是否存在（如 index.php 或 core/init.php）';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>流包装器信息泄露</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">PHP 流包装器泄露</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p>通过 <code>php://filter/convert.base64-encode/resource=</code> 包装器对源码进行 Base64 读取，造成敏感信息泄露。</p>
      <form method="get">
        <div class="field"><label>文件路径</label><input name="path" value="<?php echo h($path); ?>" placeholder="如: index.php 或 core/init.php" /></div>
        <button class="btn btn-primary" type="submit">读取</button>
      </form>
    </div>
    <div class="panel">
      <?php if($msg) echo '<p class="danger">'.h($msg).'</p>'; ?>
      <h3>读取结果（源码）</h3>
      <pre><?php echo h($content ?: '（空或读取失败）'); ?></pre>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>PHP 支持多种流包装器（<code>php://</code>、<code>phar://</code>、<code>data://</code>、<code>zip://</code> 等）。在文件读取功能未限制包装器的情况下，攻击者可用 <code>php://filter</code> 对源码进行 Base64 输出，泄露敏感逻辑与凭据。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>源码泄露：<code>php://filter/convert.base64-encode/resource=index.php</code>。</li>
        <li>与 LFI 组合：通过任意文件包含功能读取敏感文件源码。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：对路径进行严格白名单限制，仅允许特定目录与扩展名。</li>
        <li>修复：显式拒绝包装器前缀（<code>php://</code>、<code>phar://</code>、<code>data://</code> 等）。</li>
        <li>修复：关闭显示错误与目录索引，避免信息泄露扩大攻击面。</li>
      </ul>
    </div>
  </main>
</body>
</html>