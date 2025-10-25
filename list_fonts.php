<?php
$dir = __DIR__ . "/font";  // thư mục font
$files = scandir($dir);

$fonts = [];
foreach ($files as $file) {
    if (preg_match('/\.(ttf|otf|woff2?|eot)$/i', $file)) {
        $fonts[] = $file;
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($fonts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
