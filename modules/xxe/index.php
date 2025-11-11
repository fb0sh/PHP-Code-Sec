<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
$result = '';$msg='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $xml = $_POST['xml'] ?? '';
    // 演示：开启外部实体解析（危险），可读取本地文件或请求内网
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $ok = @$dom->loadXML($xml, LIBXML_NOENT | LIBXML_DTDLOAD | LIBXML_NONET);
    if ($ok) {
        $result = $dom->textContent; // 展示解析后的文本内容
    } else {
        $errs = libxml_get_errors();
        $msg = 'XML解析错误：' . h(print_r($errs, true));
        libxml_clear_errors();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>XXE 注入</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">XXE 注入</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p>示例 payload（Windows 读取 win.ini）：</p>
      <pre>&lt;!DOCTYPE data [ &lt;!ENTITY xxe SYSTEM "file:///C:/Windows/win.ini" &gt; ]&gt;
&lt;data&gt;&amp;xxe;&lt;/data&gt;</pre>
      <p>Linux 示例读取 /etc/hosts：</p>
      <pre>&lt;!DOCTYPE data [ &lt;!ENTITY xxe SYSTEM "file:///etc/hosts" &gt; ]&gt;
&lt;data&gt;&amp;xxe;&lt;/data&gt;</pre>
    </div>
    <div class="panel">
      <?php if($msg) echo '<p class="danger">'.$msg.'</p>'; ?>
      <form method="post">
        <div class="field"><label>XML 输入</label><textarea name="xml" rows="6" placeholder="在此粘贴你的 XML"></textarea></div>
        <button class="btn btn-primary" type="submit">解析</button>
      </form>
    </div>
    <?php if($result): ?>
    <div class="panel"><h3>解析结果</h3><pre><?php echo h($result); ?></pre></div>
    <?php endif; ?>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>XXE（XML 外部实体注入）在启用实体展开时，允许引用外部资源导致本地文件读取或 SSRF。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>读取本地文件：<code>file:///etc/hosts</code>、<code>file:///C:/Windows/win.ini</code>。</li>
        <li>内网探测：通过外部实体访问内网 HTTP 服务。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：禁用实体展开与网络访问，使用 <code>LIBXML_NONET</code>，避免 <code>LIBXML_NOENT</code>。</li>
        <li>修复：采用安全的解析库，限制可用的 DTD/实体。</li>
      </ul>
    </div>
  </main>
</body>
</html>