<form class="ignavo-header-search {{ $class ?? '' }}" action="{{ route('front.category') }}" method="GET">
    <input type="text" name="search" placeholder="@lang('Search popular products...')"
        value="{{ request()->input('search') }}">
    <button type="submit" aria-label="@lang('Search')">
        <i class="fas fa-search"></i>
    </button>
</form>
