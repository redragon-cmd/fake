<?php
header('Content-Type: application/json; charset=utf-8');
$raw = file_get_contents('php://input');
$body = json_decode($raw, true);
$name = $body['name'] ?? '';
$css  = $body['css']  ?? '';

if (!$name || !$css) { echo json_encode(['success'=>false,'error'=>'Thiếu name/css']); exit; }
if (!preg_match('/^[A-Za-z0-9_\-]{1,64}$/', $name)) { echo json_encode(['success'=>false,'error'=>'Tên css không hợp lệ']); exit; }

$dir = __DIR__ . '/css';
if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

$ok = @file_put_contents($dir . "/$name.css", $css);
echo json_encode(['success'=> (bool)$ok]);
