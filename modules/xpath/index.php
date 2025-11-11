<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
/*
 * 漏洞说明：XPath 注入
 * - 根因：将用户输入直接拼接到 XPath 表达式，允许通过闭合与追加查询语法选择更敏感节点或绕过条件。
 * - 复现：`alice'] | //user[role='admin` 可使查询结果包含管理员节点的 `secret`。
 * - 修复：避免字符串拼接；使用参数化 XPath/安全库；输入白名单与后端二次权限过滤。
 */
?>
<?php
$xmlData = <<<XML
<users>
  <user><name>alice</name><role>user</role><secret>alice-note-123</secret></user>
  <user><name>bob</name><role>admin</role><secret>flag-admin-bob</secret></user>
  <user><name>carol</name><role>user</role><secret>carol-note-xyz</secret></user>
</users>
XML;

$xml = simplexml_load_string($xmlData);
$q = $_GET['q'] ?? '';
$path = "//user[name='$q']/secret"; // 漏洞：直接拼接用户输入到 XPath
$result = [];
try {
    if ($q !== '') {
        $result = $xml->xpath($path) ?: [];
    }
} catch (Throwable $e) {
    $err = 'XPath 错误：' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>XPath 注入</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">XPath 注入</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p>示例数据为用户列表（包含 <code>name</code>/<code>role</code>/<code>secret</code>）。尝试注入以检索管理员密钥。</p>
      <form method="get">
        <div class="field"><label>按用户名查 secret</label><input name="q" placeholder="如: alice 或注入：alice'] | //user[role='admin" /></div>
        <button class="btn btn-primary" type="submit">查询</button>
      </form>
    </div>
    <div class="panel">
      <?php if(isset($err)) echo '<p class="danger">'.h($err).'</p>'; ?>
      <h3>XPath 路径</h3>
      <p class="muted"><code><?php echo h($path); ?></code></p>
      <h3>查询结果</h3>
      <?php if($result): ?>
        <ul>
          <?php foreach ($result as $node): ?>
            <li><code><?php echo h((string)$node); ?></code></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>（无结果或未查询）</p>
      <?php endif; ?>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>XPath 注入发生在将用户输入直接拼接到 XPath 表达式时，攻击者可构造表达式绕过条件或选择更敏感的节点。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>条件绕过：<code>alice'] | //user[role='admin</code>，使查询包含管理员节点。</li>
        <li>读取隐藏字段：改写路径提取 <code>//user[role='admin']/secret</code>。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：避免字符串拼接，使用预编译或参数化 XPath（若库支持）。</li>
        <li>修复：严格白名单输入（仅允许合法用户名字符集）。</li>
        <li>修复：按角色/权限在后端做二次过滤，避免仅靠 XPath 控制数据访问。</li>
      </ul>
    </div>
  </main>
</body>
</html>