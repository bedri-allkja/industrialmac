<?php

namespace App\Http\Controllers\Front;

use App\Classes\GeniusMailer;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class QuoteRequestController extends FrontBaseController
{
    public function create(Request $request)
    {
        $ps = $this->ps;
        $product = null;

        if ($request->filled('product')) {
            $product = Product::where('slug', $request->product)->where('status', 1)->first();
        } elseif ($request->filled('product_id')) {
            $product = Product::where('id', $request->product_id)->where('status', 1)->first();
        }

        $categories = Category::where('status', 1)->orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('frontend.quote-request', compact('ps', 'product', 'categories', 'brands'));
    }

    public function store(Request $request)
    {
        $gs = $this->gs;
        $ps = $this->ps;

        if ($gs->is_capcha == 1) {
            $request->validate([
                'g-recaptcha-response' => 'required',
            ], [
                'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
            ]);
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:50',
            'country' => 'required|string|max:255|exists:countries,country_name',
            'quantity' => 'nullable|integer|min:1',
            'message' => 'nullable|string|max:5000',
            'product_name' => 'nullable|string|max:255',
            'product_sku' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'product_id' => 'nullable|exists:products,id',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:512',
        ]);

        $product = null;
        if ($request->product_id) {
            $product = Product::find($request->product_id);
        }

        $brandName = $request->brand_name;
        if (!$brandName && $request->brand_id) {
            $brandName = Brand::find($request->brand_id)?->name;
        }
        if (!$brandName && $product?->brand) {
            $brandName = $product->brand->name;
        }

        $data = [
            'product_id' => $product?->id,
            'category_id' => $request->category_id ?: $product?->category_id,
            'brand_id' => $request->brand_id ?: $product?->brand_id,
            'customer_name' => $request->customer_name,
            'company_name' => $request->company_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'country' => $request->country,
            'product_name' => $request->product_name ?: $product?->showName(),
            'product_sku' => $request->product_sku ?: $product?->sku,
            'brand_name' => $brandName,
            'quantity' => $request->quantity ?: 1,
            'message' => $request->message,
            'status' => 'pending',
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/images/quote-requests'), $name);
            $data['image'] = $name;
        }

        $quote = QuoteRequest::create($data);

        $adminEmail = $ps->contact_email ?: $gs->from_email;
        $subject = 'New Quote Request #' . $quote->id;
        $body = "A new quote request was submitted.\n\n";
        $body .= "Request ID: {$quote->id}\n";
        $body .= "Customer: {$quote->customer_name}\n";
        $body .= "Company: {$quote->company_name}\n";
        $body .= "Email: {$quote->customer_email}\n";
        $body .= "Phone: {$quote->customer_phone}\n";
        $body .= "Country: {$quote->country}\n";
        $body .= "Product: {$quote->product_name}\n";
        $body .= "SKU/Code: {$quote->product_sku}\n";
        $body .= "Brand: {$quote->brand_name}\n";
        $body .= "Quantity: {$quote->quantity}\n";
        $body .= "Message: {$quote->message}\n";
        if ($product) {
            $body .= "Catalog product: " . url('/item/' . $product->slug) . "\n";
        }

        if ($gs->is_smtp) {
            (new GeniusMailer())->sendCustomMail([
                'to' => $adminEmail,
                'subject' => $subject,
                'body' => nl2br(e($body)),
            ]);
        } else {
            $headers = 'From: ' . $gs->from_name . ' <' . $gs->from_email . '>' . "\r\n";
            $headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
            mail($adminEmail, $subject, $body, $headers);
        }

        return back()->with('success', __('Your quote request has been sent. We will contact you shortly.'));
    }
}
