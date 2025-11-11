<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
// 演示目的：直接执行用户提供的 PHP 代码（危险示例）
if (!isset($_GET['code'])) {
    echo '缺少参数：code';
    exit;
}

$code = $_GET['code'];

try {
    // 直接 eval 用户输入，导致远程代码执行
    eval($code);
} catch (Throwable $e) {
    echo '执行错误: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}