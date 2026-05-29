<?php

/**
 * Primary site brand logo (INDUSTRIALMAC) — header, footer, invoices, favicon, etc.
 */
function site_brand_logo(): string
{
    return asset('assets/images/INDUSTRIALMAC.png');
}

function site_faq_locale(): string
{
    return app()->getLocale() === 'industrialmac_it' ? 'it' : 'en';
}

function site_faqs(?int $limit = null): array
{
    $faqs = config('faq.' . site_faq_locale());

    $items = collect($faqs['items'] ?? [])->map(function ($item) {
        return (object) [
            'title' => $item['title'],
            'details' => $item['answer'],
        ];
    });

    if ($limit !== null) {
        $items = $items->take($limit);
    }

    return $items->values()->all();
}

function site_faq_content(): array
{
    return config('faq.' . site_faq_locale(), config('faq.en'));
}

function wishlistCheck($product_id)
{
    $wishlist = \App\Models\Wishlist::where('product_id', $product_id)->where('user_id', auth()->id())->first();
    if ($wishlist) {
        return true;
    } else {
        return false;
    }

}

function addon($name)
{

    if ($name == "otp") {
        $otp = file_exists(base_path("/vendor/markury/src/Adapter/addon/otp.txt"));
        if ($otp) {
            $data = file_get_contents(base_path("/vendor/markury/src/Adapter/addon/otp.txt"));

            if ($data) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    return false;
}
