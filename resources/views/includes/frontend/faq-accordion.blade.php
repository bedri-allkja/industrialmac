@php
    $faqItems = $faqs ?? site_faqs();
    $accordionId = $accordionId ?? 'faqlist';
@endphp

<div class="accordion {{ $accordionClass ?? 'hyp-accordians accordion-flush ignavo-faq__accordion' }}" id="{{ $accordionId }}">
    @foreach ($faqItems as $key => $faq)
        <div class="accordion-item {{ $itemClass ?? 'wow-replaced' }}" @if(($itemClass ?? '') === 'wow-replaced') data-wow-delay=".1s" @endif>
            <h2 class="accordion-header">
                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#{{ $accordionId }}-{{ $key }}"
                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                    {{ $faq->title }}
                </button>
            </h2>
            <div id="{{ $accordionId }}-{{ $key }}"
                class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                data-bs-parent="#{{ $accordionId }}">
                <div class="accordion-body">
                    <p class="mb-0">{{ $faq->details }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
