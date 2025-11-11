<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
/*
 * 漏洞说明：正则拒绝服务（ReDoS）
 * - 根因：使用包含嵌套量词/回溯密集的正则模式，在特定输入上触发灾难性回溯，导致 CPU 飙高与阻塞。
 * - 复现：模式 `^(a+)+$` 与输入 `aaaa...b`；或允许用户自定义复杂正则引发拒绝服务。
 * - 修复：避免问题模式；使用原子分组/占有量词；限制输入长度与复杂度；加入超时与隔离。
 */
?>
<?php
// 演示：易致灾难性回溯的正则（ReDoS）
$pattern = $_GET['pattern'] ?? '^(a+)+$'; // 默认一个经典的回溯炸弹
$input = $_GET['input'] ?? '';
$elapsed = null; $matched = null; $pregErr = null;

if ($input !== '') {
    $start = microtime(true);
    // 限制执行时间，避免长时间阻塞。实际生产环境还应有更严格的超时与隔离策略
    @set_time_limit(2);
    $matched = @preg_match('/' . $pattern . '/u', $input);
    $elapsed = round((microtime(true) - $start) * 1000, 2);
    $err = preg_last_error();
    if ($err !== PREG_NO_ERROR) {
        $pregErr = $err;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>正则 ReDoS</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">正则拒绝服务（ReDoS）</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <p>利用含嵌套量词的正则在某些输入上产生灾难性回溯，造成 CPU 飙高与阻塞。</p>
      <form method="get">
        <div class="field"><label>正则模式</label><input name="pattern" value="<?php echo h($pattern); ?>" placeholder="如: ^(a+)+$" /></div>
        <div class="field"><label>测试输入</label><input name="input" placeholder="建议试：一长串 a 再加一个 b，如 aaaaa...b" /></div>
        <button class="btn btn-primary" type="submit">测试</button>
      </form>
    </div>
    <?php if($input !== ''): ?>
    <div class="panel">
      <h3>结果</h3>
      <p>匹配：<?php echo $matched === 1 ? '是' : ($matched === 0 ? '否' : '错误'); ?></p>
      <p>耗时：<?php echo h($elapsed); ?> ms</p>
      <?php if($pregErr !== null): ?>
        <p class="danger">preg_last_error：<?php echo h($pregErr); ?></p>
      <?php endif; ?>
      <p class="muted">提示：尝试 <code>input=aaaaaaaaaaaaaaaaaaaaaaaab</code>。</p>
    </div>
    <?php endif; ?>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>不当的正则（如含嵌套量词）在某些输入上会触发大量回溯，形成拒绝服务（ReDoS）。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>提供特制输入触发回溯炸弹：对 <code>^(a+)+$</code> 使用 <code>aaaa...b</code>。</li>
        <li>服务端允许用户控制正则模式时，直接提交复杂表达式导致 CPU 占用。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：避免嵌套量词与回溯密集的模式；使用原子分组/占有量词。</li>
        <li>修复：限制输入长度与正则复杂度；加入执行超时与隔离。</li>
        <li>修复：尽量使用线性时间的解析策略或库。</li>
      </ul>
    </div>
  </main>
</body>
</html>