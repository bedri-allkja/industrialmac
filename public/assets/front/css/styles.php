<?php

/**
 * Dynamic theme variables (replaces missing Codecanyon bundle).
 * Query: color=RRGGBB (no #), header_color=RRGGBB or named value from DB.
 */
header('Content-Type: text/css; charset=UTF-8');
header('Cache-Control: public, max-age=3600');

$hex = static function (?string $raw): string {
    if ($raw === null || $raw === '') {
        return '';
    }
    $raw = ltrim(trim($raw), '#');
    return preg_match('/^[0-9A-Fa-f]{6}$/', $raw) ? '#' . strtolower($raw) : '';
};

$primary = $hex($_GET['color'] ?? '') ?: '#2563eb';
$header = $hex($_GET['header_color'] ?? '') ?: '#0f172a';

echo <<<CSS
:root {
  --theme-primary: {$primary};
  --theme-header-bg: {$header};
}
a.text-primary, .text-primary { color: {$primary} !important; }
.btn-primary, .template-btn, .hero-shop-now-btn {
  background-color: {$primary} !important;
  border-color: {$primary} !important;
  color: #fff !important;
}
.info-bar .info-bar-btn {
  background-color: transparent !important;
  border-color: rgba(255,255,255,0.45) !important;
  color: #fff !important;
}
.btn-primary:hover, .template-btn:hover {
  filter: brightness(0.92);
}
.header-section .info-bar {
  background: {$header} !important;
}
.header-top .nav-menus > li > a:hover,
.header-top .nav-menus > li > a.active {
  color: {$primary} !important;
}
.info-bar a:hover { color: {$primary} !important; }
CSS;
