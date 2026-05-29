@php
    $benefitIcons = [
        'fa-solid fa-shield-halved',
        'fa-solid fa-truck-fast',
        'fa-solid fa-boxes-stacked',
        'fa-solid fa-headset',
    ];

    $defaultBenefits = [
        [
            'title' => __('Manufacturer warranty'),
            'details' => __('Genuine parts with manufacturer coverage'),
        ],
        [
            'title' => __('Express delivery'),
            'details' => __('Always and everywhere'),
        ],
        [
            'title' => '+1.000.000 ' . __('Managed products'),
            'details' => __('Extensive industrial catalog'),
        ],
        [
            'title' => __('Expert support'),
            'details' => __('We help you find the right part'),
        ],
    ];

    $benefits = collect($defaultBenefits);

    if (\Illuminate\Support\Facades\Schema::hasTable('services')) {
        $services = \App\Models\Service::take(4)->get();

        if ($services->isNotEmpty()) {
            $benefits = $services->values()->map(function ($service, $index) use ($defaultBenefits) {
                return [
                    'title' => $service->title,
                    'details' => $service->details,
                ];
            });
        }
    }
@endphp

<div class="ignavo-hero__benefits row g-3{{ isset($class) ? ' ' . $class : '' }}">
    @foreach ($benefits as $index => $benefit)
        <div class="col-6 col-lg-3">
            <div class="ignavo-hero__benefit h-100">
                <div class="ignavo-hero__benefit-icon" aria-hidden="true">
                    <i class="{{ $benefitIcons[$index] ?? 'fa-solid fa-circle-check' }}"></i>
                </div>
                <h6>{{ $benefit['title'] }}</h6>
                <p>{{ $benefit['details'] }}</p>
            </div>
        </div>
    @endforeach
</div>
