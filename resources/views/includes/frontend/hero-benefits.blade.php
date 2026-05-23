<div class="ignavo-hero__benefits row g-3{{ isset($class) ? ' ' . $class : '' }}">
    @if (\Illuminate\Support\Facades\Schema::hasTable('services'))
        @foreach (\App\Models\Service::take(4)->get() as $service)
            <div class="col-6 col-lg-3">
                <div class="ignavo-hero__benefit h-100">
                    <div class="ignavo-hero__benefit-icon">
                        <img src="{{ asset('assets/images/services/' . $service->photo) }}"
                            alt="{{ $service->title }}">
                    </div>
                    <div>
                        <h6>{{ $service->title }}</h6>
                        <p>{{ $service->details }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="col-6 col-lg-3">
            <div class="ignavo-hero__benefit h-100">
                <div class="ignavo-hero__benefit-icon"><i class="fas fa-shield-alt"></i></div>
                <div>
                    <h6>@lang('Manufacturer warranty')</h6>
                    <p>@lang('Genuine parts with manufacturer coverage')</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ignavo-hero__benefit h-100">
                <div class="ignavo-hero__benefit-icon"><i class="fas fa-shipping-fast"></i></div>
                <div>
                    <h6>@lang('Express delivery')</h6>
                    <p>@lang('Always and everywhere')</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ignavo-hero__benefit h-100">
                <div class="ignavo-hero__benefit-icon"><i class="fas fa-boxes"></i></div>
                <div>
                    <h6>+1.000.000 @lang('Managed products')</h6>
                    <p>@lang('Extensive industrial catalog')</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ignavo-hero__benefit h-100">
                <div class="ignavo-hero__benefit-icon"><i class="fas fa-headset"></i></div>
                <div>
                    <h6>@lang('Expert support')</h6>
                    <p>@lang('We help you find the right part')</p>
                </div>
            </div>
        </div>
    @endif
</div>
