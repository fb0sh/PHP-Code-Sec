<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
$resp = '';$msg='';
if (isset($_GET['url'])) {
    // 演示：后端请求任意URL，易 SSRF
    $u = $_GET['url'];
    $ch = curl_init($u);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    $resp = curl_exec($ch);
    if ($resp === false) { $msg = '请求失败：' . h(curl_error($ch)); }
    curl_close($ch);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SSRF</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">SSRF</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <?php if($msg) echo '<p class="danger">'.$msg.'</p>'; ?>
      <form method="get">
        <div class="field"><label>URL</label><input name="url" placeholder="http://127.0.0.1:80/ 或 http://169.254.169.254/" /></div>
        <button class="btn btn-primary" type="submit">请求</button>
      </form>
    </div>
    <?php if($resp): ?>
    <div class="panel"><h3>响应</h3><pre><?php echo h($resp); ?></pre></div>
    <?php endif; ?>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>SSRF（服务端请求伪造）是指后端根据用户可控的目标地址发起请求，导致内网服务、云平台元数据或本机敏感资源被访问与泄露。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>内网探测：<code>http://127.0.0.1</code>、<code>http://localhost</code>、<code>http://10.0.0.0/8</code> 等。</li>
        <li>云元数据：<code>http://169.254.169.254/latest/meta-data/</code>（AWS）等获取访问令牌与实例信息。</li>
        <li>危险协议与包装器：<code>file://</code>、<code>gopher://</code>、<code>ftp://</code>、<code>php://</code> 等。</li>
        <li>跳转与重绑定：通过 30x 跳转、短链接或 DNS 重绑定绕过主机校验。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：仅允许可信域名白名单（严格域名与路径），解析并校验最终主机/IP。</li>
        <li>修复：禁止访问私网地址与 <code>file/gopher/php</code> 等危险协议，统一走安全代理。</li>
        <li>修复：解析 IP 时避免十六进制、八进制、整数 IP 与 IPv6 变体绕过。</li>
      </ul>
    </div>
  </main>
</body>
</html>