<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $gs->title }}</title>
    @php
        $__localTheme = file_exists(public_path('assets/front/css/bootstrap.min.css'));
    @endphp
    @if ($__localTheme)
        @include('includes.frontend.head_assets_local')
        @if (file_exists(public_path('assets/images/INDUSTRIALMAC.png')))
            <link rel="icon" type="image/png" href="{{ site_brand_logo() }}">
        @endif
    @else
        @include('includes.frontend.head_assets_cdn')
    @endif
    @include('includes.frontend.extra_head')
    @if (! file_exists(public_path('assets/front/css/styles.php')))
        @include('includes.frontend.theme_fallback_critical')
    @endif
    <link rel="stylesheet" href="{{ asset('assets/front/css/custom.css') }}">
    @yield('css')

</head>

<body>

    @php
        $categories = App\Models\Category::with('subs')->where('status', 1)->get();
        $pages = App\Models\Page::get();
        $currencies = App\Models\Currency::all();
        $languges = App\Models\Language::all();
    @endphp
    <!-- header area -->
    @include('includes.frontend.header')

    <!-- if route is user panel then show vendor.mobile-header else show frontend.mobile_menu -->

    @php
        $url = url()->current();
        $explodeUrl = explode('/',$url);

    @endphp

    @if(in_array('user',$explodeUrl))
    <!-- frontend mobile menu -->
    @include('includes.user.mobile-header')
    @elseif(in_array("rider",$explodeUrl))
    @include('includes.rider.mobile-header')
    @else 
    @include('includes.frontend.mobile_menu')
        <!-- user panel mobile sidebar -->

    @endif
   

    <div class="overlay"></div>

    @yield('content')


    <!-- footer section -->
    @include('includes.frontend.footer')
    <!-- footer section -->

    @include('includes.frontend.cookie-consent')

    @if ($__localTheme)
        @include('includes.frontend.script_assets_local')
    @else
        @include('includes.frontend.script_assets_cdn')
    @endif


    <script>
        "use strict";
        var mainurl = "{{ url('/') }}";
        var gs      = {!! json_encode(DB::table('generalsettings')->where('id','=',1)->first(['is_loader','decimal_separator','thousand_separator','is_cookie','is_talkto','talkto'])) !!};
        var ps_category = {{ $ps->category }};

        // Fix sticky header content jump
        (function(){
            var headerTop = document.querySelector('.header-top');
            if (!headerTop) return;
            var spacer = document.createElement('div');
            spacer.className = 'sticky-spacer';
            spacer.style.display = 'none';
            headerTop.parentNode.insertBefore(spacer, headerTop.nextSibling);

            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(m) {
                    if (m.attributeName === 'class') {
                        if (headerTop.classList.contains('sticky')) {
                            spacer.style.display = 'block';
                            spacer.style.height = headerTop.offsetHeight + 'px';
                        } else {
                            spacer.style.display = 'none';
                        }
                    }
                });
            });
            observer.observe(headerTop, { attributes: true });
        })();

        var lang = {
            'days': '{{ __('Days') }}',
            'hrs': '{{ __('Hrs') }}',
            'min': '{{ __('Min') }}',
            'sec': '{{ __('Sec') }}',
            'cart_already': '{{ __('Already Added To Card.') }}',
            'cart_out': '{{ __('Out Of Stock') }}',
            'cart_success': '{{ __('Successfully Added To Cart.') }}',
            'cart_empty': '{{ __('Cart is empty.') }}',
            'coupon_found': '{{ __('Coupon Found.') }}',
            'no_coupon': '{{ __('No Coupon Found.') }}',
            'already_coupon': '{{ __('Coupon Already Applied.') }}',
            'enter_coupon': '{{ __('Enter Coupon First') }}',
            'minimum_qty_error': '{{ __('Minimum Quantity is:') }}',
            'affiliate_link_copy': '{{ __('Affiliate Link Copied Successfully') }}'
        };
    
      </script>



    @php
        if (Session::has('success')) {
            echo '<script>
                toastr.success("'.Session::get('success').'")
            </script>';
        }
        if (Session::has('unsuccess')) {
            echo '<script>
                toastr.error("'.Session::get('unsuccess').'")
            </script>';
        }
    @endphp

      
  @yield('script')

</body>

</html>
