<?php
// save_config.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

const CFG_DIR = __DIR__ . '/configs';

function respond($arr, int $code=200){ http_response_code($code); echo json_encode($arr, JSON_UNESCAPED_UNICODE); exit; }
function ensure_dir($d){ if(!is_dir($d)){ if(!@mkdir($d, 0775, true)) return false; } return is_writable($d); }
function safe_name(string $s): string{
  if (!preg_match('/^[A-Za-z0-9_\-]{1,64}$/', $s)) respond(['success'=>false,'error'=>'Tên preset chỉ cho phép A-Z, a-z, 0-9, _ và - (<=64 ký tự).'], 400);
  return $s;
}

if (!ensure_dir(CFG_DIR)) respond(['success'=>false,'error'=>'Không tạo/ghi được thư mục configs.'], 500);

$raw = file_get_contents('php://input');
if ($raw === false) respond(['success'=>false,'error'=>'Không đọc được payload.'], 400);
if (strlen($raw) > 2*1024*1024) respond(['success'=>false,'error'=>'Payload quá lớn (>2MB).'], 413);

$in = json_decode($raw, true);
if (!is_array($in)) respond(['success'=>false,'error'=>'Payload không phải JSON hợp lệ.'], 400);

$name = isset($in['name']) ? trim((string)$in['name']) : '';
$data = $in['data'] ?? null;
if ($name === '' || $data === null) respond(['success'=>false,'error'=>'Thiếu "name" hoặc "data".'], 400);
$name = safe_name($name);

$out = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
if ($out === false) respond(['success'=>false,'error'=>'Không chuyển được data sang JSON.'], 400);

$path = CFG_DIR . "/{$name}.json";
$ok = @file_put_contents($path, $out, LOCK_EX);
if ($ok === false) respond(['success'=>false,'error'=>'Ghi file thất bại (permissions?).'], 500);
@chmod($path, 0664);

respond(['success'=>true, 'file'=>"configs/{$name}.json"]);
