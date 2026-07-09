<?php

namespace App\Http\Controllers\Front;

use App\Models\Category;
use App\Models\Childcategory;
use App\Models\Product;
use App\Models\Report;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CatalogController extends FrontBaseController
{

    // CATEGORIES SECTOPN

    public function categories()
    {
        $categories = front_menu_categories();
        $cat = null;
        $subcat = null;
        $childcat = null;

        $prods = Product::with([
                'user:id,is_vendor',
                'brand:id,name,image,slug',
                'category:id,name,slug',
            ])
            ->where('status', 1)
            ->latest('id')
            ->paginate($this->gs->page_count);

        return view('frontend.products', compact('categories', 'prods', 'cat', 'subcat', 'childcat'));
    }

    // -------------------------------- CATEGORY SECTION ----------------------------------------

    public function category(Request $request, $slug = null, $slug1 = null, $slug2 = null, $slug3 = null)
    {
       
        $data['categories'] = front_menu_categories();

        if ($request->view_check) {
            session::put('view', $request->view_check);
        }

        //   dd(session::get('view'));

        $cat = null;
        $subcat = null;
        $childcat = null;
        $flash = null;
        $minprice = $request->min;
        $maxprice = $request->max;
        $sort = $request->sort;
        $search = trim((string) $request->input('search', ''));
        $pageby = $request->pageby;

        $minprice = ($minprice / $this->curr->value);
        $maxprice = ($maxprice / $this->curr->value);
        $type = $request->has('type') ?? '';

        if (!empty($slug)) {
            $cat = Category::where('slug', $slug)->firstOrFail();
            $data['cat'] = $cat;
        }

        if (!empty($slug1)) {
            $subcat = Subcategory::where('slug', $slug1)->firstOrFail();
            $data['subcat'] = $subcat;
        }
        if (!empty($slug2)) {
            $childcat = Childcategory::where('slug', $slug2)->firstOrFail();
            $data['childcat'] = $childcat;
        }

        // Sidebar "latest products" widget is identical on every category page and
        // its rating aggregates are expensive over a large catalog, so cache it.
        $data['latest_products'] = cache()->remember('front.catalog.latest_products', now()->addMinutes(15), function () {
            return Product::with('user')->whereStatus(1)->whereLatest(1)
                ->whereHas('user', function ($q) {
                    $q->where('is_vendor', 2);
                })
                ->withCount('ratings')
                ->withAvg('ratings', 'rating')
                ->take(5)
                ->get();
        });

        $prods = Product::with([
            'user:id,is_vendor',
            'brand:id,name,image,slug',
            'category:id,name,slug',
        ])->when($cat, function ($query, $cat) {
            return $query->where('category_id', $cat->id);
        })
            ->when($subcat, function ($query, $subcat) {
                return $query->where('subcategory_id', $subcat->id);
            })
            ->when($type, function ($query, $type) {
                return $query->with('user')->whereStatus(1)->whereIsDiscount(1)
                    ->where('discount_date', '>=', date('Y-m-d'))
                    ->whereHas('user', function ($user) {
                        $user->where('is_vendor', 2);
                    });
            })
            ->when($childcat, function ($query, $childcat) {
                return $query->where('childcategory_id', $childcat->id);
            })
            ->when($search !== '', function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%');
                });
            })
            ->when($minprice, function ($query, $minprice) {
                return $query->where('price', '>=', $minprice);
            })
            ->when($maxprice, function ($query, $maxprice) {
                return $query->where('price', '<=', $maxprice);
            })
            ->when($sort, function ($query, $sort) {
                if ($sort == 'date_desc') {
                    return $query->latest('id');
                } elseif ($sort == 'date_asc') {
                    return $query->oldest('id');
                } elseif ($sort == 'price_desc') {
                    return $query->latest('price');
                } elseif ($sort == 'price_asc') {
                    return $query->oldest('price');
                }
            })
            ->when(empty($sort), function ($query, $sort) {
                return $query->latest('id');
            })
            ->withCount('ratings')
            ->withAvg('ratings', 'rating');

        $prods = $prods->where(function ($query) use ($cat, $subcat, $childcat, $type, $request) {
            $flag = 0;
            if (!empty($cat)) {
                foreach ($cat->attributes as $key => $attribute) {
                    $inname = $attribute->input_name;
                    $chFilters = $request["$inname"];

                    if (!empty($chFilters)) {
                        $flag = 1;
                        foreach ($chFilters as $key => $chFilter) {
                            if ($key == 0) {
                                $query->where('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                            } else {
                                $query->orWhere('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                            }
                        }
                    }
                }
            }

            if (!empty($subcat)) {
                foreach ($subcat->attributes as $attribute) {
                    $inname = $attribute->input_name;
                    $chFilters = $request["$inname"];

                    if (!empty($chFilters)) {
                        $flag = 1;
                        foreach ($chFilters as $key => $chFilter) {
                            if ($key == 0 && $flag == 0) {
                                $query->where('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                            } else {
                                $query->orWhere('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                            }
                        }
                    }
                }
            }

            if (!empty($childcat)) {
                foreach ($childcat->attributes as $attribute) {
                    $inname = $attribute->input_name;
                    $chFilters = $request["$inname"];

                    if (!empty($chFilters)) {
                        $flag = 1;
                        foreach ($chFilters as $key => $chFilter) {
                            if ($key == 0 && $flag == 0) {
                                $query->where('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                            } else {
                                $query->orWhere('attributes', 'like', '%' . '"' . $chFilter . '"' . '%');
                            }
                        }
                    }
                }
            }
        });

        $prods = $prods->where('status', 1)
            ->paginate(isset($pageby) ? $pageby : $this->gs->page_count);

        $prods->getCollection()->transform(function ($item) {
            $item->price = $item->vendorSizePrice();

            return $item;
        });

        $data['prods'] = $prods;

        if ($search !== '' && $prods->total() === 0 && !$request->ajax()) {
            return redirect()
                ->route('front.quote', [
                    'product_name' => $search,
                    'product_sku' => $search,
                ])
                ->with('success', __('We could not find that product in our catalog. Tell us what you need and we will prepare a quote for you.'));
        }

        if ($request->ajax()) {
            $data['ajax_check'] = 1;
            return view('frontend.ajax.category', $data);
        }

        return view('frontend.products', $data);
    }

    public function search(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        if ($search === '') {
            return redirect()->route('front.categories');
        }

        return redirect()->route('front.category', ['search' => $search]);
    }

    public function getsubs(Request $request)
    {
        $category = Category::where('slug', $request->category)->firstOrFail();
        $subcategories = Subcategory::where('category_id', $category->id)->get();
        return $subcategories;
    }
    public function report(Request $request)
    {

        //--- Validation Section
        $rules = [
            'note' => 'max:400',
        ];
        $customs = [
            'note.max' => 'Note Must Be Less Than 400 Characters.',
        ];
        
        $request->validate($rules, $customs);


        $data = new Report;
        $input = $request->all();
        $data->fill($input)->save();
        return back()->with('success', 'Report has been sent successfully.');

    }
}
