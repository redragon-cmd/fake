<?php
header('Content-Type: application/json; charset=utf-8');
$name = $_GET['name'] ?? '';
if (!$name || !preg_match('/^[A-Za-z0-9_\-]{1,64}$/', $name)) { http_response_code(400); echo '{}'; exit; }
$file = __DIR__ . "/configs/$name.json";
if (!is_file($file)) { http_response_code(404); echo '{}'; exit; }
readfile($file);
