<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
/*
 * 漏洞说明：JWT 弱校验 / none 算法 / 未校验签名
 * - 根因：
 *   1) 允许 `alg=none` 或未强制指定算法；
 *   2) 错误地将非对称算法（RS256）的公钥当作对称 HMAC 密钥使用；
 *   3) 不验证签名，直接信任载荷。
 * - 复现：构造头 `{"alg":"none"}` 的令牌，载荷设置 `{"user":"admin"}`，签名留空，页面显示“有效（不安全）”。
 * - 影响：可伪造身份提升权限，绕过认证与授权逻辑。
 * - 修复：
 *   - 强制限定算法并校验签名；
 *   - 区分并正确管理对称/非对称密钥；
 *   - 校验 iss/aud/exp/nbf 等声明，最小化载荷；
 *   - 拒绝 none 算法与不完整令牌。
 */
?>
<?php
function b64url_decode($data){
    $b64 = strtr($data, '-_', '+/');
    $pad = strlen($b64) % 4; if ($pad) $b64 .= str_repeat('=', 4-$pad);
    return base64_decode($b64);
}

function insecure_jwt_decode($jwt) {
    $parts = explode('.', $jwt);
    if (count($parts) < 2) return [null,null,null,false];
    list($h64,$p64,$s64) = $parts + [null,null,null];
    $header = json_decode(b64url_decode($h64), true);
    $payload = json_decode(b64url_decode($p64), true);
    $signature = $s64 ? b64url_decode($s64) : '';
    // 漏洞点：弱校验（示例故意始终返回 true），不进行签名验证
    $valid = true; // 演示用：不安全，实际应验证签名与算法
    return [$header,$payload,$signature,$valid];
}

$msg='';$header=null;$payload=null;$signature=null;$valid=false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    list($header,$payload,$signature,$valid) = insecure_jwt_decode($token);
    if (!$header) { $msg = '<span class="danger">解析失败：令牌格式不正确</span>'; }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>JWT 弱校验/none算法</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">JWT 弱校验</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p>示例：使用 <code>{"alg":"none"}</code> 头部的令牌或伪造 HS256，将签名置空，仍被视为有效。</p>
      <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
      <form method="post">
        <div class="field"><label>JWT Token</label><textarea name="token" rows="3" placeholder="粘贴你的JWT"></textarea></div>
        <button class="btn btn-primary" type="submit">解析</button>
      </form>
    </div>
    <?php if($header): ?>
    <div class="panel"><h3>Header</h3><pre><?php echo h(print_r($header, true)); ?></pre></div>
    <div class="panel"><h3>Payload</h3><pre><?php echo h(print_r($payload, true)); ?></pre></div>
    <div class="panel"><h3>签名（未校验）</h3><pre><?php echo h(bin2hex($signature)); ?></pre></div>
    <div class="panel"><span class="status">验证状态：<?php echo $valid?'有效（不安全）':'无效'; ?></span></div>
    <?php endif; ?>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>JWT 弱校验问题包括允许 <code>alg=none</code>、错误地把公钥当作 HMAC 密钥、或不校验签名直接信任载荷。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>构造 <code>{"alg":"none"}</code> 的令牌并置空签名。</li>
        <li>将 RS256 令牌改为 HS256，使用服务器公开的证书作为 HMAC 密钥伪造。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：强制限定算法，严格进行签名校验并拒绝 <code>none</code>。</li>
        <li>修复：区分非对称与对称算法的密钥管理，不可混用。</li>
        <li>修复：校验 <code>iss/aud/exp/nbf</code> 等声明并最小化载荷。</li>
      </ul>
    </div>
  </main>
</body>
</html>