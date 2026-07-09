@foreach ($category->subs as $subcategory)
    @php
        $isSubcategoryActive = Request::segment(2) === $category->slug && Request::segment(3) === $subcategory->slug;
        $hasChilds = $subcategory->childs->count() > 0;
    @endphp
    <li>
        <div class="d-flex justify-content-between align-items-lg-baseline">
            <a href="{{ route('front.category', [$category->slug, $subcategory->slug]) }}"
                class="{{ $isSubcategoryActive ? 'sidebar-active-color' : '' }}">
                {{ $subcategory->name }}
            </a>
            @if ($hasChilds)
                <button type="button" class="im-cat-toggle {{ $isSubcategoryActive ? 'is-open' : '' }}"
                    data-target="#childsubs-{{ $subcategory->id }}">
                    <i class="fa-solid fa-plus"></i>
                    <i class="fa-solid fa-minus"></i>
                </button>
            @endif
        </div>
        @if ($hasChilds)
            <ul id="childsubs-{{ $subcategory->id }}" class="im-cat-subs ms-3 {{ $isSubcategoryActive ? 'is-open' : '' }}">
                @foreach ($subcategory->childs as $child)
                    @php
                        $isChildActive = $isSubcategoryActive && Request::segment(4) === $child->slug;
                    @endphp
                    <li>
                        <a href="{{ route('front.category', [$category->slug, $subcategory->slug, $child->slug]) }}"
                            class="{{ $isChildActive ? 'sidebar-active-color' : '' }}">
                            {{ $child->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </li>
@endforeach
