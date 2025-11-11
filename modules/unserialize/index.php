<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
// 演示：不安全反序列化 + 魔术方法
class Evil {
    public $cmd = 'dir';
    function __destruct() {
        // 演示：在析构中执行命令（极不安全）
        @system($this->cmd);
    }
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['data'] ?? '';
    try {
        // 直接反序列化用户输入（高危）
        @unserialize($data);
        $msg = '已反序列化（请在控制台或页面输出查看效果）';
    } catch (Throwable $e) {
        $msg = '<span class="danger">错误：'.h($e->getMessage()).'</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>反序列化</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">反序列化</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
      <form method="post">
        <div class="field"><label>序列化数据</label><textarea name="data" rows="3" placeholder="例如：O:4:\"Evil\":1:{s:3:\"cmd\";s:8:\"calc.exe\";}"></textarea></div>
        <button class="btn btn-primary" type="submit">反序列化</button>
      </form>
      <p class="muted">可利用魔术方法触发命令执行。实际环境中可能结合 PHAR 触发。</p>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>不安全反序列化会将外部可控的字节流还原为对象，触发类的魔术方法（如 <code>__wakeup/__destruct</code>）形成 POP 链。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>构造恶意对象图，利用已有类的魔术方法执行命令或写文件。</li>
        <li>搭配 PHAR 元数据反序列化，通过 <code>phar://</code> 触发。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：拒绝不可信的序列化输入，使用 <code>JSON</code> 等安全格式。</li>
        <li>修复：如必须反序列化，使用 <code>unserialize($s, ['allowed_classes' => false])</code> 严格限制。</li>
        <li>修复：移除危险魔术方法或对关键操作加签与鉴权。</li>
      </ul>
    </div>
  </main>
</body>
</html>