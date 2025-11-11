<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
// 参数污染(HPP)演示：相同参数出现多次，后端取值方式导致逻辑偏差
// 例如：price=100&price=1 可覆盖价格；role=user&role=admin 可越权

$price = $_GET['price'] ?? null; // 一些框架可能取第一个或最后一个值
$roles = isset($_GET['role']) ? (array)$_GET['role'] : [];
$chosenRole = $_GET['role'] ?? 'guest';

// 模拟下单与角色判定
$msg = '';
if ($price !== null) {
    $msg .= '下单金额为：' . h(is_array($price) ? end($price) : $price) . '。';
}
if (!empty($roles)) {
    $msg .= ' 角色列表接收为：' . h(print_r($roles, true));
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>HPP 参数污染</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">HPP 参数污染</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <h3>演示</h3>
      <p class="muted">构造多个同名参数，观察后端取值差异。</p>
      <ul>
        <li><code>?price=999&price=1</code> — 低价覆盖订单金额</li>
        <li><code>?role=user&role=admin</code> — 角色提升为 admin</li>
        <li><code>?role[]=user&role[]=auditor&role[]=admin</code> — 角色数组污染</li>
      </ul>
      <form method="get">
        <div class="field"><label>价格(price)</label><input name="price" placeholder="如 100" /></div>
        <div class="field"><label>角色(role)</label><input name="role" placeholder="如 user 或 admin" /></div>
        <button class="btn btn-primary" type="submit">提交</button>
      </form>
    </div>
    <div class="panel">
      <h3>后端取值</h3>
      <p>最终判定角色：<strong><?php echo h(is_array($chosenRole)? end($chosenRole): $chosenRole); ?></strong></p>
      <?php if($msg) echo '<pre>'.h($msg).'</pre>'; ?>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>HTTP 参数污染（HPP）指同名参数出现多次时，后端取值方式不一致导致业务逻辑偏差（覆盖/累加）。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>通过重复参数覆盖价格或角色。</li>
        <li>数组参数与不同解析器行为差异导致越权。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：统一参数解析策略，拒绝重复关键参数。</li>
        <li>修复：服务端强校验业务约束（价格、角色等不可被覆盖）。</li>
      </ul>
    </div>
  </main>
</body>
</html>