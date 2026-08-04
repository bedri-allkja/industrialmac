@php
    // Avoid Illuminate\Support\Str::limit — host PHP has no mbstring, so
    // mb_strimwidth() fatals and product pages time out / 500.
    $seoClip = static function ($text, int $max = 160): string {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $text)) ?? '');
        if ($text === '') {
            return '';
        }
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($text, 'UTF-8') > $max ? mb_substr($text, 0, $max, 'UTF-8') : $text;
        }

        return strlen($text) > $max ? substr($text, 0, $max) : $text;
    };
    $seoLocale = function_exists('site_content_locale') ? site_content_locale() : 'en';
    $canonical = url()->current();
    $rawDesc = $seoClip($seo->meta_description ?? '', 300);
    $defaultDescription = ($rawDesc !== '' && strlen($rawDesc) >= 40 && !str_contains(strtolower($rawDesc), 'genius'))
        ? $rawDesc
        : 'Industrialmac - global B2B supplier of industrial spare parts, automation components, MRO products and technical solutions. Request a quote worldwide.';
    $defaultKeywords = (!empty($seo->meta_keys) && !str_contains(strtolower((string) $seo->meta_keys), 'genius'))
        ? $seo->meta_keys
        : 'industrial spare parts, automation components, MRO, B2B industrial supply, Industrialmac, Brescia';
    $siteName = $gs->title ?? 'Industrialmac';
    $ogImage = site_brand_logo();
    $pageTitle = 'Industrialmac | Industrial Spare Parts & Automation Components';
    $pageDescription = $defaultDescription;
    $pageKeywords = $defaultKeywords;
@endphp

@if (isset($page->meta_tag) || isset($page->meta_description) || isset($page->title))
    @php
        $pageTitle = trim(($page->title ?? $siteName) . ' | ' . $siteName);
        $pageDescription = $page->meta_description ?: $defaultDescription;
        $pageKeywords = $page->meta_tag ?: $defaultKeywords;
    @endphp
@elseif (isset($blog))
    @php
        $pageTitle = trim(($blog->title ?? 'Blog') . ' | ' . $siteName);
        $pageDescription = $blog->meta_description ?: $seoClip(substr((string) ($blog->details ?? ''), 0, 2000), 160);
        $pageKeywords = $blog->meta_tag ?: $defaultKeywords;
        if (!empty($blog->photo)) {
            $ogImage = asset('assets/images/blogs/' . $blog->photo);
        }
    @endphp
@elseif (isset($productt))
    @php
        $productName = method_exists($productt, 'showName') ? $productt->showName() : ($productt->name ?? 'Product');
        $fullProductName = (string) ($productt->name ?? $productName);
        $pageTitle = $fullProductName . (!empty($productt->sku) ? ' | ' . $productt->sku : '') . ' | ' . $siteName;
        $brandLabel = trim((string) optional($productt->brand)->name);
        $namePrefix = ($brandLabel !== '' && stripos($fullProductName, $brandLabel) === false)
            ? $brandLabel . ' '
            : '';
        $fallbackProductDesc = trim(
            $namePrefix
            . $fullProductName
            . (!empty($productt->sku) ? ' (SKU ' . $productt->sku . ')' : '')
            . ' - request a quote from Industrialmac. Original industrial spare parts, worldwide B2B supply.'
        );
        $rawMeta = trim(strip_tags((string) ($productt->meta_description ?? '')));
        // Skip imported Turkish / broken encodings (??? / � / common TR words).
        $metaLooksBroken = $rawMeta === ''
            || preg_match('/[�?]{2,}|fiyat|teslim|markan|ürün|ürünün|ürününü|adl[ıi]/u', $rawMeta);
        $pageDescription = !$metaLooksBroken ? $seoClip($rawMeta, 160) : $seoClip($fallbackProductDesc, 160);
        $metaTags = $productt->meta_tag ?? null;
        if (is_array($metaTags)) {
            $pageKeywords = implode(',', array_filter($metaTags));
        } elseif (!empty($metaTags)) {
            $pageKeywords = (string) $metaTags;
        } else {
            $pageKeywords = trim($productName . ', ' . ($productt->sku ?? '') . ', industrial parts, Industrialmac', ' ,');
        }
        try {
            $ogImage = function_exists('product_list_image_url') ? product_list_image_url($productt) : site_brand_logo();
        } catch (\Throwable $e) {
            $ogImage = site_brand_logo();
        }
    @endphp
@elseif (isset($cat) && !empty($cat->name))
    @php
        $pageTitle = $cat->name . ' | Brands | ' . $siteName;
        $pageDescription = 'Browse ' . $cat->name . ' industrial products and request a quote from Industrialmac.';
    @endphp
@elseif (request()->routeIs('front.categories'))
    @php
        $pageTitle = 'Industrial Brands | ' . $siteName;
        $pageDescription = 'Explore the Industrialmac global brand network - thousands of industrial brands and products for B2B sourcing.';
    @endphp
@elseif (request()->routeIs('front.about'))
    @php
        $pageTitle = 'About Industrialmac | ' . $siteName;
        $pageDescription = 'Industrialmac is your global partner for industrial spare parts, automation components and MRO supply.';
    @endphp
@elseif (request()->routeIs('front.quality'))
    @php
        $pageTitle = 'Quality Policy | ' . $siteName;
        $pageDescription = 'Industrialmac quality policy - committed to reliable industrial supply and continuous improvement.';
    @endphp
@elseif (request()->routeIs('front.faq') || request()->routeIs('front.qa'))
    @php
        $pageTitle = 'Questions & Answers | ' . $siteName;
        $pageDescription = 'Frequently asked questions about sourcing industrial parts with Industrialmac.';
    @endphp
@elseif (request()->routeIs('front.contact') || request()->routeIs('front.quote'))
    @php
        $pageTitle = 'Contact / Request a Quote | ' . $siteName;
        $pageDescription = 'Contact Industrialmac or request a quote for industrial spare parts and automation components.';
    @endphp
@elseif (request()->routeIs('front.privacy'))
    @php
        $pageTitle = 'Privacy Policy | ' . $siteName;
        $pageDescription = 'Privacy Policy of Industrialmac S.r.l. - GDPR information for www.industrialmac.com.';
    @endphp
@elseif (request()->routeIs('front.cookie'))
    @php
        $pageTitle = 'Cookie Policy | ' . $siteName;
        $pageDescription = 'Cookie Policy of Industrialmac S.r.l. for www.industrialmac.com and www.industrialmac.it.';
    @endphp
@elseif (request()->routeIs('front.legal'))
    @php
        $pageTitle = 'Legal Notice | ' . $siteName;
        $pageDescription = 'Legal Notice for the Industrialmac website - company information, trademarks and terms of use.';
    @endphp
@endif

@php
    $metaDescription = $seoClip($pageDescription, 160);
    $schemaDescription = $seoClip($pageDescription, 300);
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="keywords" content="{{ $pageKeywords }}">
<meta name="author" content="Industrialmac S.r.l.">
<meta name="robots" content="index, follow, max-image-preview:large">
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:type" content="{{ isset($productt) ? 'product' : 'website' }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="{{ $seoLocale === 'it' ? 'it_IT' : ($seoLocale === 'es' ? 'es_ES' : ($seoLocale === 'ru' ? 'ru_RU' : 'en_US')) }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $ogImage }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">

@if (isset($productt))
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => method_exists($productt, 'showName') ? $productt->showName() : ($productt->name ?? ''),
    'sku' => $productt->sku ?? null,
    'mpn' => $productt->sku ?? null,
    'image' => [$ogImage],
    'description' => $schemaDescription,
    'brand' => [
        '@type' => 'Brand',
        'name' => optional($productt->brand)->name ?: optional($productt->category)->name ?: 'Industrialmac',
    ],
    'offers' => [
        '@type' => 'Offer',
        'url' => $canonical,
        'availability' => 'https://schema.org/InStock',
        'priceCurrency' => 'EUR',
        'seller' => [
            '@type' => 'Organization',
            'name' => 'Industrialmac S.r.l.',
        ],
    ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
</script>
@endif

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'Industrialmac S.r.l.',
    'url' => url('/'),
    'logo' => site_brand_logo(),
    'email' => 'info@industrialmac.it',
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => 'Via Della Volta 37',
        'addressLocality' => 'Brescia',
        'postalCode' => '25124',
        'addressCountry' => 'IT',
    ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
</script>

@if ($default_font && $default_font->font_value)
    <link
        href="https://fonts.googleapis.com/css?family={{ $default_font->font_value }}:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
@else
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
@endif

@if (file_exists(public_path('assets/front/css/styles.php')))
    <link rel="stylesheet"
        href="{{ asset('assets/front/css/styles.php?color=' . str_replace('#', '', $gs->colors) . '&header_color=' . $gs->header_color) }}">
@endif
@if (file_exists(public_path('assets/front/css/font.php')))
    @if ($default_font && $default_font->font_family)
        <link rel="stylesheet" id="colorr"
            href="{{ asset('assets/front/css/font.php?font_familly=' . $default_font->font_family) }}">
    @else
        <link rel="stylesheet" id="colorr"
            href="{{ asset('assets/front/css/font.php?font_familly=' . ' Open Sans') }}">
    @endif
@endif

@if (!empty($seo->google_analytics))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $seo->google_analytics }}"></script>
    <script>
        "use strict";
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', '{{ $seo->google_analytics }}');
    </script>
@endif
@if (!empty($seo->facebook_pixel))
    <script>
        "use strict";

        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $seo->facebook_pixel }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id={{ $seo->facebook_pixel }}&ev=PageView&noscript=1" />
    </noscript>
@endif
