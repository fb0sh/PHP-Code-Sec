<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
// 演示：反射任意 Origin 并允许凭据（极不安全）
if (isset($_GET['origin'])) {
    header('Access-Control-Allow-Origin: ' . $_GET['origin']);
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

$data = [
    'user' => current_user()['username'] ?? 'guest',
    'secret' => 'admin-token-123',
    'note' => '该接口错误地允许跨域凭据，且可反射任意Origin。'
];
echo json_encode($data, JSON_UNESCAPED_UNICODE);
exit;
?>