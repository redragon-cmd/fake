<?php
// get_config.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

const CFG_DIR = __DIR__ . '/configs';

function respond($arr, int $code=200){ http_response_code($code); echo json_encode($arr, JSON_UNESCAPED_UNICODE); exit; }
function safe_name(string $s): string{
  if (!preg_match('/^[A-Za-z0-9_\-]{1,64}$/', $s)) respond(['error'=>'Tên preset không hợp lệ.'], 400);
  return $s;
}

$name = isset($_GET['name']) ? trim((string)$_GET['name']) : '';
if ($name === '') respond(['error'=>'Thiếu tên preset.'], 400);
$name = safe_name($name);

$path = CFG_DIR . "/{$name}.json";
if (!is_file($path)) respond(['error'=>'Không tìm thấy preset.'], 404);

$raw = @file_get_contents($path);
if ($raw === false) respond(['error'=>'Không đọc được file.'], 500);

$data = json_decode($raw, true);
if ($data === null && json_last_error() !== JSON_ERROR_NONE){
  respond(['error'=>'Nội dung JSON bị hỏng.'], 500);
}
respond($data);
