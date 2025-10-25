<?php
// list_configs.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$dir = __DIR__ . '/configs';
$out = [];

if (is_dir($dir)) {
  $files = glob($dir.'/*.json');
  // sort theo mtime DESC
  usort($files, function($a,$b){ return filemtime($b) <=> filemtime($a); });
  foreach ($files as $f) {
    $base = basename($f, '.json');
    // chỉ tên hợp lệ
    if (preg_match('/^[A-Za-z0-9_\-]{1,64}$/', $base)) $out[] = $base;
  }
}

echo json_encode($out, JSON_UNESCAPED_UNICODE);
