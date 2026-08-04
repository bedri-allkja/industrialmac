<li>
    @php $pendingQuotes = \App\Models\QuoteRequest::where('status', 'pending')->count(); @endphp
    <a href="{{ route('admin-quote-index') }}" class="wave-effect">
        <i class="fas fa-file-invoice"></i>{{ __('Quote Requests') }}
        @if ($pendingQuotes > 0)
            <span class="badge badge-danger" style="background:#e5352b;color:#fff;border-radius:10px;padding:2px 7px;font-size:11px;margin-left:6px;">{{ $pendingQuotes }}</span>
        @endif
    </a>
</li>

<li>
    <a href="#menu1" class="accordion-toggle wave-effect" data-toggle="collapse" aria-expanded="false">
        <i class="fas fa-flag"></i>{{ __('Manage Country') }}
    </a>
    <ul class="collapse list-unstyled" id="menu1" data-parent="#accordion">
        <li>
            <a href="{{ route('admin-country-index') }}"><span>{{ __('Country') }}</span></a>
        </li>
    </ul>
</li>


<li>
    <a href="#menu5" class="accordion-toggle wave-effect" data-toggle="collapse" aria-expanded="false"><i
            class="fas fa-sitemap"></i>{{ __('Manage Categories') }}</a>
    <ul class="collapse list-unstyled
        @if (request()->is('admin/attribute/*/manage') && request()->input('type') == 'category') show
        @elseif(request()->is('admin/attribute/*/manage') && request()->input('type') == 'subcategory')
          show
        @elseif(request()->is('admin/attribute/*/manage') && request()->input('type') == 'childcategory')
          show
        @elseif(request()->is('admin/brand*'))
          show @endif"
        id="menu5" data-parent="#accordion">
        <li class="@if (request()->is('admin/attribute/*/manage') && request()->input('type') == 'category') active @endif">
            <a href="{{ route('admin-cat-index') }}"><span>{{ __('Main Category') }}</span></a>
        </li>
        <li class="@if (request()->is('admin/attribute/*/manage') && request()->input('type') == 'subcategory') active @endif">
            <a href="{{ route('admin-subcat-index') }}"><span>{{ __('Sub Category') }}</span></a>
        </li>
        <li class="@if (request()->is('admin/attribute/*/manage') && request()->input('type') == 'childcategory') active @endif">
            <a href="{{ route('admin-childcat-index') }}"><span>{{ __('Child Category') }}</span></a>
        </li>
        <li class="@if (request()->is('admin/brand*')) active @endif">
            <a href="{{ route('admin-brand-index') }}"><span>{{ __('Brands') }}</span></a>
        </li>
    </ul>
</li>

<li>
    <a href="#menu2" class="accordion-toggle wave-effect" data-toggle="collapse" aria-expanded="false">
        <i class="icofont-cart"></i>{{ __('Products') }}
    </a>
    <ul class="collapse list-unstyled" id="menu2" data-parent="#accordion">
        <li>
            <a href="{{ route('admin-prod-types') }}"><span>{{ __('Add New Product') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-prod-index') }}"><span>{{ __('All Products') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-prod-deactive') }}"><span>{{ __('Deactivated Product') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-prod-catalog-index') }}"><span>{{ __('Product Catalogs') }}</span></a>
        </li>

        <li>
            <a href="{{ route('admin-gs-prod-settings') }}"><span>{{ __('Product Settings') }}</span></a>
        </li>
    </ul>
</li>

<li>
    <a href="{{ route('admin-prod-import') }}"><i class="fas fa-upload"></i>{{ __('Bulk Product Upload') }}</a>
</li>

<li>
    <a href="{{ route('admin-prod-scraper') }}"><i class="fas fa-spider"></i>{{ __('Product Scraper') }}</a>
</li>

<li>
    <a href="#msg" class="accordion-toggle wave-effect" data-toggle="collapse" aria-expanded="false">
        <i class="fas fa-fw fa-newspaper"></i>{{ __('Messages') }}
    </a>
    <ul class="collapse list-unstyled" id="msg" data-parent="#accordion">
        <li>
            <a href="{{ route('admin-message-index') }}"><span>{{ __('Tickets') }}</span></a>
        </li>
    </ul>
</li>

<li>
    <a href="#blog" class="accordion-toggle wave-effect" data-toggle="collapse" aria-expanded="false">
        <i class="fas fa-fw fa-newspaper"></i>{{ __('Blog') }}
    </a>
    <ul class="collapse list-unstyled" id="blog" data-parent="#accordion">
        <li>
            <a href="{{ route('admin-cblog-index') }}"><span>{{ __('Categories') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-blog-index') }}"><span>{{ __('Posts') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-gs-blog-settings') }}"><span>{{ __('Blog Settings') }}</span></a>
        </li>
    </ul>
</li>

<li>
    <a href="#general" class="accordion-toggle wave-effect" data-toggle="collapse" aria-expanded="false">
        <i class="fas fa-cogs"></i>{{ __('General Settings') }}
    </a>
    <ul class="collapse list-unstyled" id="general" data-parent="#accordion">
        <li>
            <a href="{{ route('admin-gs-logo') }}"><span>{{ __('Logo') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-gs-fav') }}"><span>{{ __('Favicon') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-gs-contents') }}"><span>{{ __('Website Contents') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-gs-popup') }}"><span>{{ __('Popup Banner') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-gs-bread') }}"><span>{{ __('Breadcrumb Banner') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-gs-error-banner') }}"><span>{{ __('Error Banner') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-gs-maintenance') }}"><span>{{ __('Website Maintenance') }}</span></a>
        </li>
    </ul>
</li>

<li>
    <a href="#homepage" class="accordion-toggle wave-effect" data-toggle="collapse" aria-expanded="false">
        <i class="fas fa-edit"></i>{{ __('Home Page Settings') }}
    </a>
    <ul class="collapse list-unstyled" id="homepage" data-parent="#accordion">
        <li>
            <a href="{{ route('admin-home-page-index') }}"><span>{{ __('Home Pages') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-sl-index') }}"><span>{{ __('Sliders') }}</span></a>
        </li>

        <li>
            <a href="{{ route('admin-arrival-index') }}"><span>{{ __('Best Month Offer') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-partner-index') }}"><span>{{ __('Partners') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-ps-customize') }}"><span>{{ __('Home Page Customization') }}</span></a>
        </li>
    </ul>
</li>

<li>
    <a href="#menu" class="accordion-toggle wave-effect" data-toggle="collapse" aria-expanded="false">
        <i class="fas fa-file-code"></i>{{ __('Menu Page Settings') }}
    </a>
    <ul class="collapse list-unstyled" id="menu" data-parent="#accordion">
        <li>
            <a href="{{ route('admin-faq-index') }}"><span>{{ __('FAQ Page') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-ps-contact') }}"><span>{{ __('Contact Us Page') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-page-index') }}"><span>{{ __('Other Pages') }}</span></a>
        </li>

        <li>
            <a href="{{ route('admin-ps-menu-links') }}"><span>{{ __('Customize Menu Links') }}</span></a>
        </li>
    </ul>
</li>

<li>
    <a href="#emails" class="accordion-toggle wave-effect" data-toggle="collapse" aria-expanded="false">
        <i class="fas fa-at"></i>{{ __('Email Settings') }}
    </a>
    <ul class="collapse list-unstyled" id="emails" data-parent="#accordion">
        <li><a href="{{ route('admin-mail-index') }}"><span>{{ __('Email Template') }}</span></a></li>
        <li><a href="{{ route('admin-mail-config') }}"><span>{{ __('Email Configurations') }}</span></a></li>
        <li><a href="{{ route('admin-group-show') }}"><span>{{ __('Group Email') }}</span></a></li>
    </ul>
</li>


@if (addon('otp'))
    <li>
        <a href="#otp" class="accordion-toggle wave-effect" data-toggle="collapse" aria-expanded="false">
            <i class="fas fa-sms"></i>{{ __('Sms Settings') }}
        </a>
        <ul class="collapse list-unstyled" id="otp" data-parent="#accordion">
            <li><a href="{{ route('admin-otp-config') }}"><span>{{ __('OTP Configurations') }}</span></a></li>
        </ul>
    </li>
@endif






<li>
    <a href="#socials" class="accordion-toggle wave-effect" data-toggle="collapse" aria-expanded="false">
        <i class="fas fa-paper-plane"></i>{{ __('Social Settings') }}
    </a>
    <ul class="collapse list-unstyled" id="socials" data-parent="#accordion">
        <li><a href="{{ route('admin-sociallink-index') }}"><span>{{ __('Social Links') }}</span></a></li>
    </ul>
</li>

<li>
    <a href="#langs" class="accordion-toggle wave-effect" data-toggle="collapse" aria-expanded="false">
        <i class="fas fa-language"></i>{{ __('Language Settings') }}
    </a>
    <ul class="collapse list-unstyled" id="langs" data-parent="#accordion">
        <li><a href="{{ route('admin-lang-index') }}"><span>{{ __('Website Language') }}</span></a></li>
        <li><a href="{{ route('admin-tlang-index') }}"><span>{{ __('Admin Panel Language') }}</span></a></li>

    </ul>
</li>

<li>
    <a href="{{ route('admin.fonts.index') }}" class=" wave-effect"><i
            class="fa fa-font"></i>{{ __('Font Option') }}</a>
</li>

<li>
    <a href="#seoTools" class="accordion-toggle wave-effect" data-toggle="collapse" aria-expanded="false">
        <i class="fas fa-wrench"></i>{{ __('SEO Tools') }}
    </a>
    <ul class="collapse list-unstyled" id="seoTools" data-parent="#accordion">
        <li>
            <a href="{{ route('admin-prod-popular', 30) }}"><span>{{ __('Popular Products') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-seotool-analytics') }}"><span>{{ __('Google Analytics') }}</span></a>
        </li>
        <li>
            <a href="{{ route('admin-seotool-keywords') }}"><span>{{ __('Website Meta Keywords') }}</span></a>
        </li>
    </ul>
</li>

<li>
    <a href="{{ route('admin-staff-index') }}" class=" wave-effect"><i
            class="fas fa-user-secret"></i>{{ __('Manage
                                Staffs') }}</a>
</li>

<li>
    <a href="{{ route('admin-role-index') }}" class=" wave-effect"><i
            class="fas fa-user-tag"></i>{{ __('Manage Roles') }}</a>
</li>

<li>
    <a href="{{ route('admin-cache-clear') }}" class=" wave-effect"><i
            class="fas fa-sync"></i>{{ __('Clear Cache') }}</a>
</li>
