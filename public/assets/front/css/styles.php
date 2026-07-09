<?php

/**
 * Dynamic theme variables – refined industrial design.
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

$primary = $hex($_GET['color'] ?? '') ?: '#1a5276';
$header  = $hex($_GET['header_color'] ?? '') ?: '#154360';

echo <<<CSS
:root {
  --primary: #13294b;
  --primary-dark: #0d1d38;
  --primary-darker: #081326;
  --accent: #2e6ab8;
  --accent-light: #4a8ad4;
  --light-bg: #eef3fb;
  --card-bg: #ffffff;
  --text: #1b2733;
  --text-secondary: #46586b;
  --text-muted: #7b8a99;
  --border: #d3dbe6;
  --border-light: #e6ecf4;
  --footer-bg: #10233d;
  --footer-dark: #0a1729;
  --success: #12a150;
  --warning: #f5a30a;
  --danger: #e5352b;
  --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
  --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
  --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
  --radius: 6px;
  --radius-lg: 10px;
  --transition: all 0.25s ease;
}

/* ── Base ─────────────────────────────────────────── */
body {
  font-family: 'Inter', 'Open Sans', 'Jost', -apple-system, BlinkMacSystemFont, sans-serif;
  color: var(--text);
  background: var(--card-bg);
  line-height: 1.6;
}
a { color: var(--accent); text-decoration: none; }
a:hover { color: var(--primary); }

/* ── Buttons ──────────────────────────────────────── */
.btn-primary, .template-btn, .hero-shop-now-btn {
  background: var(--primary) !important;
  border-color: var(--primary) !important;
  color: #fff !important;
  border-radius: var(--radius) !important;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 13px;
  padding: 11px 28px;
  transition: var(--transition);
  box-shadow: 0 2px 6px rgba(19,41,75,0.28);
}
.btn-primary:hover, .template-btn:hover, .hero-shop-now-btn:hover {
  background: var(--primary-dark) !important;
  border-color: var(--primary-dark) !important;
  box-shadow: 0 4px 12px rgba(19,41,75,0.38);
  filter: none;
}
.template-btn.dark-btn {
  background: var(--text) !important;
  border-color: var(--text) !important;
}
.template-btn.dark-btn:hover {
  background: var(--primary-darker) !important;
  border-color: var(--primary-darker) !important;
}

/* ── Top Info Bar ─────────────────────────────────── */
.header-section .info-bar {
  background: var(--primary-darker) !important;
  padding: 6px 0;
  font-size: 12px;
  letter-spacing: 0.2px;
}
.info-bar a, .info-bar .dropdown-toggle {
  color: rgba(255,255,255,0.85) !important;
  font-size: 12px;
}
.info-bar a:hover { color: #fff !important; }

/* ── Main Header / Nav ────────────────────────────── */
.header-section .header-top {
  background: var(--primary) !important;
  padding: 10px 0;
  transition: var(--transition);
}
.header-section .header-top.sticky {
  background: var(--primary) !important;
  box-shadow: 0 2px 16px rgba(0,0,0,0.15);
  padding: 8px 0;
  z-index: 9999;
}
.header-section.header-stikcy { min-height: 0; }
body.sticky-active { padding-top: var(--sticky-header-h, 0px); }

.header-section .header-top .nav-menus > li > a,
.header-section .header-top .has-submenu > a,
.header-section .header-top .has-megamenu > a {
  color: #fff !important;
  font-weight: 600;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  padding: 8px 14px;
}
.header-section .header-top .nav-menus > li > a:hover,
.header-section .header-top .nav-menus > li > a.active {
  color: rgba(255,255,255,0.75) !important;
}
.header-section .header-top .has-submenu-icon svg path,
.header-section .header-top .has-megamenu .has-submenu-icon svg path { fill: #fff; }
.header-logo-wrapper .logo {
  max-height: 64px !important;
  max-width: 300px !important;
  width: auto !important;
  height: auto !important;
  object-fit: contain;
}
.mobile-menu-brand-logo {
  max-height: 48px !important;
  max-width: 260px !important;
  width: auto !important;
  height: auto !important;
  object-fit: contain;
}
.gs-footer-section .left-info > a > img.logo {
  max-height: 56px !important;
  max-width: 280px !important;
  width: auto !important;
  height: auto !important;
  object-fit: contain;
}
.vendor-res-header-logo {
  max-height: 56px !important;
  max-width: 260px !important;
  width: auto !important;
  object-fit: contain;
}
.header-logo-wrapper .logo-text { color: #fff !important; }
.header-section .header-top .nav-right .icon-circle button svg path,
.header-section .header-top .nav-right .icon-circle a svg path { stroke: #fff; }
.header-section .header-top .nav-right .icon-circle .cart-count {
  background: var(--warning);
  color: #fff;
  font-size: 10px;
  font-weight: 700;
}
.header-section .header-top .header-toggle svg path { stroke: #fff; }

/* Sticky state */
.header-top.sticky .nav-menus > li > a,
.header-top.sticky .has-submenu > a,
.header-top.sticky .has-megamenu > a { color: #fff !important; }
.header-top.sticky .nav-right .icon-circle button svg path,
.header-top.sticky .nav-right .icon-circle a svg path { stroke: #fff !important; }
.header-top.sticky .header-toggle svg path { stroke: #fff !important; }
.header-top.sticky .header-logo-wrapper .logo-text { color: #fff !important; }

/* Search bar */
.esporim-search-bar {
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.25);
  border-radius: var(--radius);
  overflow: hidden;
  display: flex;
  align-items: center;
  max-width: 300px;
  height: 36px;
  transition: var(--transition);
}
.esporim-search-bar:focus-within {
  background: rgba(255,255,255,0.95);
  border-color: rgba(255,255,255,0.5);
}
.esporim-search-bar input {
  border: none;
  outline: none;
  padding: 6px 12px;
  font-size: 13px;
  flex: 1;
  color: #fff;
  background: transparent;
}
.esporim-search-bar:focus-within input { color: var(--text); }
.esporim-search-bar input::placeholder { color: rgba(255,255,255,0.6); }
.esporim-search-bar:focus-within input::placeholder { color: var(--text-muted); }
.esporim-search-bar button {
  background: transparent;
  border: none;
  padding: 6px 12px;
  cursor: pointer;
  display: flex;
  align-items: center;
}
.esporim-search-bar button svg path { stroke: rgba(255,255,255,0.8); }
.esporim-search-bar:focus-within button svg path { stroke: var(--primary); }

/* Megamenu & dropdowns */
.header-section .megamenu,
.header-section .dropdown-menu {
  border: 1px solid var(--border);
  box-shadow: var(--shadow-lg);
  border-radius: var(--radius);
}

/* ── Hero Section ─────────────────────────────────── */
/* Wallpaper on the Slick root: fade mode offsets slides with negative left; a per-slide bg would not cover the viewport. */
.hero-slider-wrapper {
  width: 100%;
  max-width: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
  position: relative;
  background-color: #0f172a;
  background-size: cover !important;
  background-position: center center !important;
  background-repeat: no-repeat !important;
  box-shadow: inset 0 0 0 9999px rgba(15, 23, 42, 0.42);
}
.hero-slider-wrapper.slick-slider .slick-list {
  margin: 0;
  background: transparent;
}
.hero-slider-wrapper .slick-track {
  background: transparent;
}
.hero-slider-wrapper .slick-slide {
  width: 100% !important;
  max-width: 100%;
  box-sizing: border-box;
}
.hero-slider-wrapper .slick-slide .gs-hero-section {
  width: 100%;
  max-width: 100%;
  box-sizing: border-box;
}
.hero-slider-wrapper .gs-hero-section {
  background-color: transparent !important;
  background-image: none !important;
  box-shadow: none;
}
.gs-hero-section {
  width: 100%;
  max-width: 100%;
  min-height: 440px;
  position: relative;
  display: flex;
  align-items: center;
}
.gs-hero-section::before {
  display: none;
}
.gs-hero-section .container { position: relative; z-index: 2; }
.gs-hero-section .hero-content { padding: 50px 0; }
.gs-hero-section .hero-content .subtitle {
  color: var(--accent-light) !important;
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 3px;
  margin-bottom: 12px;
}
.gs-hero-section .hero-content .title {
  color: #fff !important;
  font-size: 40px;
  line-height: 1.15;
  font-weight: 800;
  margin-bottom: 16px;
}
.gs-hero-section .hero-content .des {
  color: rgba(255,255,255,0.85) !important;
  font-size: 15px;
  line-height: 1.7;
  max-width: 460px;
}
.gs-hero-section .hero-shop-now-btn {
  margin-top: 24px !important;
  padding: 13px 38px;
  font-size: 14px;
  background: #fff !important;
  color: var(--primary) !important;
  border-color: #fff !important;
  font-weight: 700;
  border-radius: var(--radius) !important;
}
.gs-hero-section .hero-shop-now-btn:hover {
  background: var(--accent-light) !important;
  color: #fff !important;
  border-color: var(--accent-light) !important;
}

/* ── Feature Boxes ────────────────────────────────── */
.esporim-features {
  background: var(--card-bg);
  padding: 30px 0;
  border-bottom: 1px solid var(--border-light);
}
.esporim-feature-box {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 16px;
}
.esporim-feature-box .feature-icon {
  width: 46px;
  height: 46px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary);
}
.esporim-feature-box h6 {
  font-size: 14px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 2px;
}
.esporim-feature-box p {
  font-size: 12px;
  color: var(--text-secondary);
  margin: 0;
}

/* ── Section Titles ───────────────────────────────── */
.esporim-section-title {
  font-size: 20px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 24px;
  padding-bottom: 10px;
  border-bottom: 3px solid var(--primary);
  display: inline-block;
}
.esporim-section-title a { color: var(--text); text-decoration: none; }
.esporim-section-title a:hover { color: var(--primary); }

/* ── Brands Grid ──────────────────────────────────── */
.esporim-brands {
  padding: 50px 0;
  background: var(--card-bg);
}
.esporim-brands .brand-item {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 85px;
  transition: var(--transition);
}
.esporim-brands .brand-item:hover {
  box-shadow: var(--shadow-md);
  border-color: var(--accent);
}
.esporim-brands .brand-item img {
  max-height: 45px;
  max-width: 100%;
  object-fit: contain;
}

/* ── Product Cards ────────────────────────────────── */
.single-product {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  transition: var(--transition);
  height: 100%;
  display: flex;
  flex-direction: column;
}
.single-product:hover {
  box-shadow: var(--shadow-md);
  border-color: var(--accent);
}
.single-product .img-wrapper {
  background: var(--light-bg);
  padding: 0;
  display: block;
  aspect-ratio: 1 / 1;
  position: relative;
  overflow: hidden;
}
.single-product .img-wrapper .img-link {
  display: block;
  width: 100%;
  height: 100%;
}
.single-product .img-wrapper .product-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  max-height: none;
  max-width: 100%;
}
.single-product .content-wrapper {
  padding: 12px 14px;
  border-top: 1px solid var(--border-light);
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.single-product .product-brand-logo {
  max-height: 18px;
  margin-bottom: 6px;
  object-fit: contain;
}
.single-product .product-brand-name {
  font-size: 11px;
  color: var(--text-muted);
  text-transform: uppercase;
  font-weight: 600;
  margin-bottom: 4px;
}
.single-product .product-title {
  font-size: 13px;
  font-weight: 600;
  color: var(--text);
  line-height: 1.4;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.single-product .add-to-cart {
  opacity: 0;
  transition: opacity 0.3s;
}
.single-product:hover .add-to-cart { opacity: 1; }

/* ── Categories Page ──────────────────────────────── */
.gs-product-sidebar-wrapper .single-product-widget {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 20px;
  margin-bottom: 20px;
}
.gs-product-sidebar-wrapper .widget-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--text);
  padding-bottom: 10px;
  border-bottom: 2px solid var(--primary);
  margin-bottom: 14px;
}
.product-nav-wrapper {
  background: var(--light-bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 12px 16px;
}

/* ── Product Detail Page ──────────────────────────── */
.single-product-details-content-wrapper { padding: 40px 0; }
.product-info-wrapper h3 {
  font-size: 22px;
  font-weight: 700;
  color: var(--text);
}
.product-stocks-wraper ul li {
  padding: 6px 0;
  border-bottom: 1px solid var(--border-light);
  font-size: 14px;
}
.tab-product-des-wrapper .nav-tabs {
  border-bottom: 2px solid var(--border);
}
.tab-product-des-wrapper .nav-tabs .nav-link {
  color: var(--text-secondary);
  font-weight: 600;
  font-size: 14px;
  text-transform: uppercase;
  border: none;
  border-bottom: 3px solid transparent;
  padding: 10px 20px;
}
.tab-product-des-wrapper .nav-tabs .nav-link.active {
  color: var(--primary);
  border-bottom-color: var(--primary);
  background: transparent;
}
.product-breadcrumb {
  display: flex;
  gap: 8px;
  list-style: none;
  padding: 0;
  font-size: 13px;
}
.product-breadcrumb li + li::before {
  content: '›';
  margin-right: 8px;
  color: var(--text-muted);
}
.product-breadcrumb li a { color: var(--text-secondary); }
.product-breadcrumb li:last-child a { color: var(--text); font-weight: 600; }

/* ── Breadcrumb Section ───────────────────────────── */
.gs-breadcrumb-section {
  background: var(--primary) !important;
  padding: 30px 0 !important;
  min-height: auto !important;
}
.gs-breadcrumb-section .breadcrumb-title { font-size: 24px; color: #fff; }
.gs-breadcrumb-section .bread-menu li a { color: rgba(255,255,255,0.8); }

/* ── CTA Banner ───────────────────────────────────── */
.esporim-cta {
  background: var(--light-bg);
  padding: 50px 0;
  text-align: center;
}
.esporim-cta h5 {
  color: var(--text-secondary);
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 10px;
  font-weight: 600;
}
.esporim-cta h2 {
  font-size: 26px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 10px;
}
.esporim-cta .stars { color: var(--warning); font-size: 20px; }

/* ── Footer ───────────────────────────────────────── */
.gs-footer-section {
  background: var(--footer-bg) !important;
  color: #fff;
}
.gs-footer-section .newslatter { display: none !important; }
.gs-footer-section .footer-row { padding: 50px 0 30px; }
.gs-footer-section .footer-row h5 {
  color: #fff;
  font-size: 15px;
  font-weight: 700;
  margin-bottom: 20px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.gs-footer-section .footer-row a,
.gs-footer-section .footer-category-links li a {
  color: rgba(255,255,255,0.65) !important;
  font-size: 14px;
  transition: var(--transition);
}
.gs-footer-section .footer-row a:hover,
.gs-footer-section .footer-category-links li a:hover {
  color: #fff !important;
}
.gs-footer-section .left-info a {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}
.gs-footer-section .gs-footer-bottom {
  background: var(--footer-dark);
  padding: 16px 0;
}
.gs-footer-section .gs-footer-bottom p {
  color: rgba(255,255,255,0.5);
  font-size: 12px;
  margin: 0;
}
.gs-footer-section .footer-disclaimer {
  padding: 20px 0 0;
  border-top: 1px solid rgba(255,255,255,0.08);
  margin-top: 20px;
}
.gs-footer-section .footer-disclaimer p {
  color: rgba(255,255,255,0.35);
  font-size: 11px;
  line-height: 1.6;
}

/* ── Mobile Menu ──────────────────────────────────── */
.mobile-menu { background: var(--footer-bg); }
.mobile-menu .mobile-menu-top { background: var(--primary); }
.mobile-menu .state-left-btn,
.mobile-menu .state-right-btn {
  background: var(--primary) !important;
  border-color: rgba(255,255,255,0.15) !important;
}
.mobile-menu .active-tab-btn.active {
  background: var(--primary-dark) !important;
}

/* ── Misc ─────────────────────────────────────────── */
.gs-cate-section { padding: 30px 0; }
.gs-explore-product-section { padding: 50px 0; }
.gs-partner-section { padding: 50px 0; }
.gs-service-section .service-row { padding: 30px 0; }
.gs-title-box .title { font-size: 22px; font-weight: 700; color: var(--text); }
.explore-tab-navbar .nav-link {
  color: var(--text-secondary);
  font-weight: 600;
  border-bottom: 3px solid transparent;
}
.explore-tab-navbar .nav-link.active {
  color: var(--primary);
  border-bottom-color: var(--primary);
}
.sidebar-active-color { color: var(--primary) !important; }

/* ── Product grid sliders ─────────────────────────── */
.gs-explore-product-section .product-cards-slider { margin: 0 -8px; }
.gs-explore-product-section .product-cards-slider .single-product { margin: 0 8px; }

/* ── Responsive ───────────────────────────────────── */
@media (max-width: 991px) {
  .gs-hero-section { min-height: 340px; }
  .gs-hero-section .hero-content .title { font-size: 28px; }
  .esporim-search-bar { max-width: 100%; }
}
@media (max-width: 767px) {
  .gs-hero-section { min-height: 280px; }
  .hero-slider-wrapper { background-position: center center !important; }
  .gs-hero-section .hero-content .title { font-size: 22px; }
  .gs-hero-section .hero-content { padding: 24px 0; }
  .esporim-feature-box { padding: 10px 8px; }
}
CSS;
