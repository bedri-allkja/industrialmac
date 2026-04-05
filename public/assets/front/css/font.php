<?php

header('Content-Type: text/css; charset=UTF-8');
header('Cache-Control: public, max-age=3600');

$family = isset($_GET['font_familly']) ? (string) $_GET['font_familly'] : 'Open Sans';
$family = str_replace(["\0", '"', "'", '\\'], '', $family);
$family = trim($family) ?: 'Open Sans';

echo 'body, .body-font { font-family: "' . $family . '", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif !important; }';
