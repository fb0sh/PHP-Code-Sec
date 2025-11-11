<?php require_once __DIR__ . '/core/init.php'; ?>
<?php
session_unset();
session_destroy();
header('Location: /');
exit;
?>