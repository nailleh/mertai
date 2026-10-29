<?php
$p = $_GET['p'] ?? 'chat';
$p = preg_match('/^[a-z0-9-]+$/', $p) ? $p : 'chat';
$file = __DIR__ . "/pages/$p.php";
if (!is_file($file)) { http_response_code(404); $file = __DIR__ . '/pages/chat.php'; }
$v = filemtime(__DIR__ . '/assets/css/app.css');
?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="dark">
<title>Mert AI</title>
<link rel="icon" href="assets/img/mert.jpeg">
<link rel="stylesheet" href="assets/css/app.css?v=<?= $v ?>">
</head>
<body>
<?php include $file; ?>
<script src="assets/js/app.js?v=<?= $v ?>" defer></script>
</body>
</html>
