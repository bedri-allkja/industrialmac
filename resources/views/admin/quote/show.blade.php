@extends('layouts.admin')

@section('content')
    <div class="content-area">
        <div class="mr-breadcrumb">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="heading">{{ __('Quote Request') }} #{{ $data->id }}</h4>
                    <ul class="links">
                        <li><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li><a href="{{ route('admin-quote-index') }}">{{ __('Quote Requests') }}</a></li>
                        <li><a href="{{ route('admin-quote-show', $data->id) }}">#{{ $data->id }}</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="add-product-content1">
            <div class="row">
                <div class="col-lg-12">
                    @include('alerts.admin.form-success')

                    <div class="product-description">
                        <div class="body-area">
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="table-responsive show-table">
                                        <table class="table">
                                            <tr>
                                                <th>{{ __('Customer') }}</th>
                                                <td>{{ $data->customer_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Company') }}</th>
                                                <td>{{ $data->company_name ?: '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Email') }}</th>
                                                <td><a href="mailto:{{ $data->customer_email }}">{{ $data->customer_email }}</a></td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Phone') }}</th>
                                                <td>{{ $data->customer_phone ?: '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Country') }}</th>
                                                <td>{{ $data->country ?: '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Product') }}</th>
                                                <td>{{ $data->product_name ?: '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('SKU / Code') }}</th>
                                                <td>{{ $data->product_sku ?: '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Brand') }}</th>
                                                <td>{{ $data->brand_name ?: ($data->brand->name ?? '-') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Category') }}</th>
                                                <td>{{ $data->category->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Quantity') }}</th>
                                                <td>{{ $data->quantity }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Submitted') }}</th>
                                                <td>{{ $data->created_at->format('d M Y, H:i') }}</td>
                                            </tr>
                                            @if ($data->product_id)
                                                <tr>
                                                    <th>{{ __('Catalog Product') }}</th>
                                                    <td>
                                                        @if ($data->product && $data->product->slug)
                                                            <a href="{{ route('front.product', $data->product->slug) }}" target="_blank">
                                                                {{ $data->product->showName() }}
                                                            </a>
                                                        @else
                                                            #{{ $data->product_id }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>

                                    @if ($data->message)
                                        <h5 class="mt-4">{{ __('Message') }}</h5>
                                        <p class="border rounded p-3 bg-light">{{ $data->message }}</p>
                                    @endif

                                    @if ($data->image)
                                        <h5 class="mt-4">{{ __('Uploaded Image') }}</h5>
                                        <a href="{{ asset('assets/images/quote-requests/' . $data->image) }}" target="_blank">
                                            <img src="{{ asset('assets/images/quote-requests/' . $data->image) }}"
                                                alt="{{ __('Quote request image') }}"
                                                style="max-width:240px;border-radius:6px;border:1px solid #ddd;">
                                        </a>
                                    @endif
                                </div>

                                <div class="col-lg-5">
                                    <div class="p-4 border rounded bg-white">
                                        <h5>{{ __('Update Status') }}</h5>
                                        <form action="{{ route('admin-quote-status', $data->id) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <select name="status" class="form-control" required>
                                                    @foreach (['pending', 'processing', 'completed', 'cancelled'] as $status)
                                                        <option value="{{ $status }}" @selected($data->status === $status)>
                                                            {{ ucfirst($status) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">{{ __('Save Status') }}</button>
                                        </form>

                                        <form action="{{ route('admin-quote-delete', $data->id) }}" method="POST" class="mt-3"
                                            onsubmit="return confirm('{{ __('Delete this quote request?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">{{ __('Delete Request') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
