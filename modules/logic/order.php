<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
$msg=''; $paid=false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 演示：价格由前端传入且未校验，逻辑漏洞导致下单金额可被任意篡改
    $item = $_POST['item'] ?? 'VIP会员';
    $price = $_POST['price'] ?? '199';
    $paid = true;
    $msg = '下单成功：' . h($item) . '，金额：' . h($price) . ' 元（未校验）';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>逻辑漏洞 - 价格篡改</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">逻辑漏洞</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
      <form method="post">
        <div class="field"><label>商品</label><input name="item" value="VIP会员" /></div>
        <div class="field"><label>价格（可被前端篡改）</label><input name="price" value="199" /></div>
        <button class="btn btn-primary" type="submit">下单</button>
      </form>
      <p class="muted">后端不可信任前端参数，需根据商品ID查询真实价格。</p>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>业务逻辑漏洞出现在将关键参数（如价格、权限）留给前端控制，服务端未校验真实值与用户权限。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>前端改价：把隐藏域的价格修改为低价。</li>
        <li>改动角色/折扣等参数绕过校验。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：关键数据由服务端按商品ID查库，拒绝前端传值。</li>
        <li>修复：订单验签、权限鉴定与风控。</li>
      </ul>
    </div>
  </main>
</body>
</html>