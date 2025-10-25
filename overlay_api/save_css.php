<?php
// save_css.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

const CSS_DIR = __DIR__ . '/css';

function respond($arr, int $code=200){ http_response_code($code); echo json_encode($arr, JSON_UNESCAPED_UNICODE); exit; }
function ensure_dir($d){ if(!is_dir($d)){ if(!@mkdir($d, 0775, true)) return false; } return is_writable($d); }
function safe_name(string $s): string{
  if (!preg_match('/^[A-Za-z0-9_\-]{1,64}$/', $s)) respond(['success'=>false,'error'=>'Tên chỉ cho phép A-Z, a-z, 0-9, _ và - (<=64).'], 400);
  return $s;
}

if (!ensure_dir(CSS_DIR)) respond(['success'=>false,'error'=>'Không tạo/ghi được thư mục css.'], 500);

$raw = file_get_contents('php://input');
if ($raw === false) respond(['success'=>false,'error'=>'Không đọc được payload.'], 400);
if (strlen($raw) > 2*1024*1024) respond(['success'=>false,'error'=>'Payload quá lớn (>2MB).'], 413);

$in = json_decode($raw, true);
if (!is_array($in)) respond(['success'=>false,'error'=>'Payload không phải JSON hợp lệ.'], 400);

$name = isset($in['name']) ? trim((string)$in['name']) : '';
$css  = isset($in['css'])  ? (string)$in['css'] : null;
if ($name === '' || $css === null) respond(['success'=>false,'error'=>'Thiếu "name" hoặc "css".'], 400);
$name = safe_name($name);
if (strlen($css) > 800*1024) respond(['success'=>false,'error'=>'CSS quá lớn (>800KB).'], 413);

$path = CSS_DIR . "/{$name}.css";
$ok = @file_put_contents($path, $css, LOCK_EX);
if ($ok === false) respond(['success'=>false,'error'=>'Ghi file thất bại (permissions?).'], 500);
@chmod($path, 0664);

respond(['success'=>true, 'file'=>"css/{$name}.css"]);
