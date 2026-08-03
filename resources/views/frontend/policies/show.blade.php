@extends('layouts.front')

@section('content')
<section class="gs-breadcrumb-section bg-class"
    data-background="{{ $gs->breadcrumb_banner ? asset('assets/images/'.$gs->breadcrumb_banner) : asset('assets/images/noimage.png') }}">
    <div class="container">
        <div class="row justify-content-center content-wrapper">
            <div class="col-12">
                <h2 class="breadcrumb-title">{{ $title }}</h2>
                <ul class="bread-menu">
                    <li><a href="{{ route('front.index') }}">@lang('Home')</a></li>
                    <li><a href="#">{{ $title }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<div class="gs-blog-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="gs-blog-card policy-page">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h3 class="mb-2">{{ $title }}</h3>
                            <p class="text-muted mb-0">{{ $subtitle }}</p>
                        </div>
                        @if (!empty($pdfUrl))
                        <a href="{{ $pdfUrl }}" class="template-btn" target="_blank" rel="noopener">
                            {{ $locale === 'it' ? 'Scarica PDF' : __('Download PDF') }}
                        </a>
                        @endif
                    </div>

                    <div class="policy-meta small text-muted mb-4">
                        @if ($locale === 'it')
                            <div><strong>Sito web:</strong> www.industrialmac.it (e www.industrialmac.com)</div>
                            <div><strong>Titolare del trattamento:</strong> Industrialmac S.r.l. — Via Della Volta 37, 25124 Brescia (BS), Italia</div>
                            <div><strong>P. IVA / Codice Fiscale:</strong> IT04234730986</div>
                            <div><strong>Email:</strong> <a href="mailto:info@industrialmac.it">info@industrialmac.it</a></div>
                            <div><strong>Data di efficacia:</strong> 29/07/2026</div>
                        @else
                            <div><strong>Website:</strong> www.industrialmac.it (and www.industrialmac.com)</div>
                            <div><strong>Data Controller:</strong> Industrialmac S.r.l. — Via Della Volta 37, 25124 Brescia (BS), Italy</div>
                            <div><strong>VAT / Fiscal Code:</strong> IT04234730986</div>
                            <div><strong>Contact Email:</strong> <a href="mailto:info@industrialmac.it">info@industrialmac.it</a></div>
                            <div><strong>Effective Date:</strong> July 29, 2026</div>
                        @endif
                    </div>

                    <div class="policy-content">
                        @include($partial)
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .policy-page { background: #fff; padding: 2rem; border-radius: 8px; }
    .policy-content h4 { margin-top: 1.75rem; margin-bottom: .75rem; font-size: 1.15rem; }
    .policy-content p, .policy-content li { line-height: 1.7; color: #333; }
    .policy-content ul { padding-left: 1.25rem; margin-bottom: 1rem; }
    .policy-content table { width: 100%; border-collapse: collapse; margin: 1rem 0 1.5rem; font-size: .95rem; }
    .policy-content th, .policy-content td { border: 1px solid #e5e5e5; padding: .65rem .75rem; vertical-align: top; }
    .policy-content th { background: #f7f7f7; }
</style>
@endsection
