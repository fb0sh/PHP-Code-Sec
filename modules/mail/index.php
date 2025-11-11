<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
$msg='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['to'] ?? '';
    $subj = $_POST['subj'] ?? 'Test';
    $from = $_POST['from'] ?? 'noreply@example.com';
    $body = $_POST['body'] ?? 'Hello';
    // 演示：邮件头注入（CRLF），部分环境发送可能失败，这里仍调用 mail
    $headers = "From: " . $from;
    $ok = @mail($to, $subj, $body, $headers);
    $msg = $ok ? '已尝试发送（查看服务器邮件配置）' : '<span class="danger">发送失败或未配置邮件服务</span>';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>邮件头注入</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">邮件头注入</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
      <form method="post">
        <div class="field"><label>收件人</label><input name="to" placeholder="victim@example.com" /></div>
        <div class="field"><label>主题</label><input name="subj" value="Hello" /></div>
        <div class="field"><label>发件人（可注入）</label><input name="from" placeholder="noreply@example.com\r\nBCC: bad@evil.com" /></div>
        <div class="field"><label>内容</label><textarea name="body" rows="3">测试</textarea></div>
        <button class="btn btn-primary" type="submit">发送</button>
      </form>
      <p class="muted">此处未过滤 CRLF，可能造成邮件头注入。</p>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>邮件头注入源于未过滤的 CRLF 导致控制邮件头字段（如 BCC、CC、From），可用于垃圾邮件与钓鱼。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>在 <code>From</code>/<code>Subject</code> 等字段插入 <code>\r\n</code> 追加头。</li>
        <li>利用弱校验的地址与显示名，伪造身份。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：使用成熟邮件库并严格校验地址，拒绝 CRLF。</li>
        <li>修复：仅允许白名单发件人，添加签名与 DMARC/SPF/DKIM 配置。</li>
      </ul>
    </div>
  </main>
</body>
</html>