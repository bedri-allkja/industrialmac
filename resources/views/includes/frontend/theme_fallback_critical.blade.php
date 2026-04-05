{{-- Shipped theme CSS lives under public/assets/front (styles.php, style.css). When that folder is missing, Bootstrap alone cannot style gs-* classes or typo utility classes used in Blade. --}}
@php
    $primary = $gs->colors ?? '#2563eb';
    $headerBg = $gs->header_color ?? '#1e293b';
@endphp
<style id="theme-fallback-critical">
    :root {
        --gs-primary: {{ $primary }};
        --gs-header: {{ $headerBg }};
    }

    .custom-containerr {
        max-width: 1320px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 12px;
        padding-right: 12px;
    }

    .d-nonee {
        display: none !important;
    }

    @media (min-width: 768px) {
        .d-md-blockk {
            display: block !important;
        }
    }

    @media (min-width: 992px) {
        .d-lg-blockk {
            display: block !important;
        }
    }

    .header-section {
        background: var(--gs-header);
        color: #fff;
    }

    .info-bar {
        font-size: 0.875rem;
        padding: 0.35rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    .info-bar a,
    .header-section a {
        color: #fff;
        text-decoration: none;
    }

    .info-bar ul.wows,
    .header-section ul.wows {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
    }

    .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.9);
    }

    .gs-hero-section {
        min-height: 320px;
        padding: 3rem 0;
        background: #f1f5f9 center/cover no-repeat;
        background-color: #e2e8f0;
    }

    .gs-title-box .title {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .gs-title-box .des {
        color: #64748b;
    }

    .template-btn {
        display: inline-block;
        padding: 0.5rem 1.25rem;
        background: var(--gs-primary);
        color: #fff !important;
        border-radius: 0.375rem;
        text-decoration: none;
        font-weight: 500;
    }

    .template-btn:hover {
        filter: brightness(0.95);
        color: #fff !important;
    }

    .single-product {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        overflow: hidden;
        margin-bottom: 1rem;
        background: #fff;
    }

    .single-product .img-wrapper {
        position: relative;
        background: #f8fafc;
    }

    .single-product .product-img {
        width: 100%;
        aspect-ratio: 1;
        object-fit: cover;
    }

    .single-product .content-wrapper {
        padding: 0.75rem 1rem 1rem;
    }

    .single-product .product-title {
        font-size: 0.95rem;
        margin: 0;
    }

    .gs-partner-section .single-partner,
    .gs-partnerss .single-partner {
        padding: 1rem;
        text-align: center;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        background: #fff;
    }

    .gs-breadcrumb-section {
        padding: 2.5rem 0;
        background: #f1f5f9 center/cover no-repeat;
        background-color: #e2e8f0;
    }

    .breadcrumb-title {
        font-size: 1.5rem;
        font-weight: 600;
    }

    .bread-menu {
        list-style: none;
        padding: 0;
        margin: 0.5rem 0 0;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .overlay {
        display: none;
    }
</style>
