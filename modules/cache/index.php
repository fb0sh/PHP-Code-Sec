<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
/*
 * 漏洞说明：Web 缓存投毒与欺骗
 * - 根因：缓存键/Vary 未包含影响响应的参数或头部（如 `promo`、`User-Agent`），动态内容被缓存并影响其他用户。
 * - 复现：对同一 `page` 写入不同 `promo`，观察同一缓存槽位被污染。
 * - 修复：将影响响应的参数/头纳入缓存键；对动态页面禁用或细粒度缓存；正确设置 `Content-Type` 与路由，避免静态化欺骗；输出编码与 CSP。
 */
?>
<?php
// 演示：Web 缓存投毒与欺骗（模拟缓存键未包含变动参数/头，导致被投毒）
$page = $_GET['page'] ?? 'home';
$promo = $_GET['promo'] ?? ''; // 变动参数，但未纳入缓存键（示例）
$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

// 模拟缓存：仅按 page 缓存，不考虑 promo/UA
$cache_key = 'cache_' . preg_replace('/[^a-z0-9_-]/i', '', $page);
$content = "<h3>页面：" . h($page) . "</h3>" .
           "<p>促销标记：" . h($promo) . "</p>" .
           "<p class='muted'>UA：" . h($ua) . "（未纳入缓存键示例）</p>";

// 响应头：模拟错误的缓存策略（真实系统中由网关/反向代理处理）
header('Cache-Control: public, max-age=60');
header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Web 缓存投毒与欺骗</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">Web 缓存投毒/欺骗</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p>此演示模拟缓存键未包含 <code>promo</code> 参数与部分头信息，导致动态内容被缓存并影响其他用户（投毒）。</p>
      <form method="get">
        <div class="field"><label>page</label><input name="page" value="<?php echo h($page); ?>" placeholder="如: home 或 news" /></div>
        <div class="field"><label>promo</label><input name="promo" placeholder="如: &lt;script&gt;alert('poison')&lt;/script&gt; 或攻击性文案" /></div>
        <button class="btn btn-primary" type="submit">渲染并缓存</button>
      </form>
    </div>
    <div class="panel">
      <h3>缓存键</h3>
      <p><code><?php echo h($cache_key); ?></code></p>
      <h3>渲染结果（可能被缓存）</h3>
      <div><?php echo $content; ?></div>
      <p class="muted">说明：缓存键只包含 <code>page</code>，因此不同 <code>promo</code> 值可被写入同一缓存槽位，造成投毒。</p>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>Web 缓存投毒/欺骗发生在反向代理或 CDN 缓存键未包含影响响应的参数或头部时，攻击者可提交带有恶意内容的请求，使动态响应被缓存并提供给其他用户。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>参数未纳入缓存键：<code>promo</code> 等可影响响应却未包含在缓存键中。</li>
        <li>欺骗静态化：错误的 <code>Content-Type</code> 或路由伪装导致动态页面以静态资源形式缓存。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：将影响响应的参数/头纳入缓存键（Vary），或对动态页面禁用缓存。</li>
        <li>修复：正确设置 <code>Content-Type</code> 与路由，避免将敏感响应作为静态资源缓存。</li>
        <li>修复：对可注入内容进行输出编码、CSP 等多层防护。</li>
      </ul>
    </div>
  </main>
</body>
</html>