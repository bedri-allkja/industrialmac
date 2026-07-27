@extends('layouts.admin')

@section('styles')
<style>
.im-dash { --im-ink:#0f172a; --im-muted:#64748b; --im-line:#e2e8f0; --im-accent:#0ea5e9; --im-warn:#f59e0b; --im-ok:#10b981; --im-danger:#ef4444; }
.im-dash .im-stat {
    background:#fff; border:1px solid var(--im-line); border-radius:14px; padding:18px 18px 16px;
    display:flex; align-items:flex-start; justify-content:space-between; gap:12px; height:100%;
    box-shadow:0 1px 2px rgba(15,23,42,.04); transition:transform .15s ease, box-shadow .15s ease;
}
.im-dash .im-stat:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(15,23,42,.06); }
.im-dash .im-stat .label { color:var(--im-muted); font-size:13px; font-weight:600; margin:0 0 6px; text-transform:uppercase; letter-spacing:.03em; }
.im-dash .im-stat .value { color:var(--im-ink); font-size:28px; font-weight:700; line-height:1.1; margin:0 0 8px; }
.im-dash .im-stat .link { font-size:13px; font-weight:600; color:var(--im-accent); text-decoration:none; }
.im-dash .im-stat .icon {
    width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:18px; flex-shrink:0;
}
.im-dash .im-stat.accent .icon { background:linear-gradient(135deg,#0284c7,#38bdf8); }
.im-dash .im-stat.warn .icon { background:linear-gradient(135deg,#d97706,#fbbf24); }
.im-dash .im-stat.ok .icon { background:linear-gradient(135deg,#059669,#34d399); }
.im-dash .im-stat.ink .icon { background:linear-gradient(135deg,#1e293b,#64748b); }
.im-dash .im-panel {
    background:#fff; border:1px solid var(--im-line); border-radius:14px; overflow:hidden;
    box-shadow:0 1px 2px rgba(15,23,42,.04); height:100%;
}
.im-dash .im-panel-h {
    padding:14px 18px; border-bottom:1px solid var(--im-line); display:flex; align-items:center; justify-content:space-between; gap:10px;
}
.im-dash .im-panel-h h5 { margin:0; font-size:15px; font-weight:700; color:var(--im-ink); }
.im-dash .im-panel-b { padding:16px 18px; }
.im-dash .im-table { width:100%; margin:0; }
.im-dash .im-table th {
    font-size:12px; text-transform:uppercase; letter-spacing:.04em; color:var(--im-muted);
    border-top:0; border-bottom:1px solid var(--im-line); font-weight:700; padding:10px 8px;
}
.im-dash .im-table td { vertical-align:middle; border-top:1px solid #f1f5f9; padding:12px 8px; font-size:13px; color:#334155; }
.im-dash .badge-soft {
    display:inline-block; border-radius:999px; padding:4px 10px; font-size:11px; font-weight:700; text-transform:capitalize;
}
.im-dash .badge-soft.pending { background:#fff7ed; color:#c2410c; }
.im-dash .badge-soft.processing { background:#eff6ff; color:#1d4ed8; }
.im-dash .badge-soft.completed { background:#ecfdf5; color:#047857; }
.im-dash .badge-soft.declined,
.im-dash .badge-soft.cancelled { background:#fef2f2; color:#b91c1c; }
.im-dash .prod-thumb { width:42px; height:42px; object-fit:contain; border-radius:8px; background:#f8fafc; border:1px solid var(--im-line); }
.im-dash canvas { max-width:100%; }
</style>
@endsection

@section('content')
<div class="content-area im-dash">
    @include('alerts.form-success')

    @if($activation_notify != "")
    <div class="alert alert-danger validation">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h3 class="text-center">{!! clean($activation_notify, array('Attr.EnableID' => true)) !!}</h3>
    </div>
    @endif

    @if(Session::has('cache'))
    <div class="alert alert-success validation">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h3 class="text-center">{{ Session::get("cache") }}</h3>
    </div>
    @endif

    <div class="row row-cards-one">
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="im-stat warn">
                <div>
                    <p class="label">{{ __('Pending Quotes') }}</p>
                    <p class="value">{{ $pending_quotes }}</p>
                    <a class="link" href="{{ route('admin-quote-index') }}">{{ __('View quotes') }}</a>
                </div>
                <div class="icon"><i class="fas fa-file-invoice"></i></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="im-stat accent">
                <div>
                    <p class="label">{{ __('Quotes (30 days)') }}</p>
                    <p class="value">{{ $quotes_month }}</p>
                    <a class="link" href="{{ route('admin-quote-index') }}">{{ __('Open list') }}</a>
                </div>
                <div class="icon"><i class="fas fa-chart-line"></i></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="im-stat ok">
                <div>
                    <p class="label">{{ __('Products') }}</p>
                    <p class="value">{{ number_format($products) }}</p>
                    <a class="link" href="{{ route('admin-prod-index') }}">{{ __('Manage products') }}</a>
                </div>
                <div class="icon"><i class="icofont-cart-alt"></i></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="im-stat ink">
                <div>
                    <p class="label">{{ __('Brands') }}</p>
                    <p class="value">{{ number_format($brands) }}</p>
                    <a class="link" href="{{ route('admin-brand-index') }}">{{ __('Manage brands') }}</a>
                </div>
                <div class="icon"><i class="fas fa-tags"></i></div>
            </div>
        </div>
    </div>

    <div class="row row-cards-one">
        <div class="col-lg-8 mb-3">
            <div class="im-panel">
                <div class="im-panel-h">
                    <h5>{{ __('Quote requests — last 30 days') }}</h5>
                </div>
                <div class="im-panel-b">
                    <canvas id="quoteTrendChart" height="110"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="im-panel">
                <div class="im-panel-h">
                    <h5>{{ __('Quote status') }}</h5>
                </div>
                <div class="im-panel-b">
                    <canvas id="quoteStatusChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards-one">
        <div class="col-lg-7 mb-3">
            <div class="im-panel">
                <div class="im-panel-h">
                    <h5>{{ __('Recent quote requests') }}</h5>
                    <a class="link" href="{{ route('admin-quote-index') }}">{{ __('View all') }}</a>
                </div>
                <div class="im-panel-b p-0">
                    <div class="table-responsive">
                        <table class="table im-table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_quotes as $quote)
                                <tr>
                                    <td>{{ $quote->id }}</td>
                                    <td>
                                        <strong>{{ $quote->customer_name }}</strong>
                                        @if($quote->company_name)
                                            <div class="text-muted" style="font-size:12px;">{{ $quote->company_name }}</div>
                                        @endif
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($quote->product_name ?: '—', 40) }}</td>
                                    <td><span class="badge-soft {{ $quote->status }}">{{ $quote->status }}</span></td>
                                    <td class="text-right">
                                        <a href="{{ route('admin-quote-show', $quote->id) }}"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">{{ __('No quote requests yet.') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-3">
            <div class="im-panel">
                <div class="im-panel-h">
                    <h5>{{ __('Most viewed products') }}</h5>
                    <a class="link" href="{{ route('admin-prod-index') }}">{{ __('Products') }}</a>
                </div>
                <div class="im-panel-b p-0">
                    <div class="table-responsive">
                        <table class="table im-table mb-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Views') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($poproducts as $product)
                                <tr>
                                    <td>
                                        <img class="prod-thumb" loading="lazy"
                                            src="{{ filter_var($product->photo, FILTER_VALIDATE_URL) ? $product->photo : asset('assets/images/products/'.$product->photo) }}"
                                            alt="">
                                    </td>
                                    <td>
                                        <a href="{{ route('admin-prod-edit', $product->id) }}">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($product->name), 42) }}
                                        </a>
                                        <div class="text-muted" style="font-size:12px;">{{ $product->category->name ?? '' }}</div>
                                    </td>
                                    <td>{{ number_format($product->views) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    "use strict";

    var labels = @json($days);
    var quoteSeries = @json($quote_series);
    var status = @json($status_breakdown);

    var trend = document.getElementById('quoteTrendChart');
    if (trend) {
        new Chart(trend, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '{{ __("Quotes") }}',
                    data: quoteSeries,
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14,165,233,.15)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 8, color: '#94a3b8' } },
                    y: { beginAtZero: true, ticks: { precision: 0, color: '#94a3b8' }, grid: { color: '#f1f5f9' } }
                }
            }
        });
    }

    var statusEl = document.getElementById('quoteStatusChart');
    if (statusEl) {
        new Chart(statusEl, {
            type: 'doughnut',
            data: {
                labels: ['{{ __("Pending") }}', '{{ __("Processing") }}', '{{ __("Completed") }}', '{{ __("Cancelled") }}'],
                datasets: [{
                    data: [status.pending || 0, status.processing || 0, status.completed || 0, status.cancelled || 0],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, color: '#64748b' } }
                },
                cutout: '62%'
            }
        });
    }
})();
</script>
@endsection
