<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Product;
use Datatables;
use Illuminate\Http\Request;
use Validator;

class BrandsController extends AdminBaseController
{
    public function datatables()
    {
        $datas = Brand::query()->orderByDesc('id')->get();

        return Datatables::of($datas)
            ->addColumn('logo', function (Brand $data) {
                if ($data->image) {
                    $url = asset('assets/images/brands/' . $data->image);

                    return '<img src="' . $url . '" alt="" style="max-height:40px;max-width:80px;object-fit:contain;">';
                }
                if ($data->photo) {
                    $url = asset('assets/images/brands/' . $data->photo);

                    return '<img src="' . $url . '" alt="" style="max-height:40px;max-width:80px;object-fit:contain;">';
                }

                return '<span class="text-muted">—</span>';
            })
            ->editColumn('is_featured', function (Brand $data) {
                $class = (int) $data->is_featured === 1 ? 'drop-success' : 'drop-danger';
                $s = (int) $data->is_featured === 1 ? 'selected' : '';
                $ns = (int) $data->is_featured === 0 ? 'selected' : '';

                return '<div class="action-list"><select class="process select droplinks ' . $class . '"><option data-val="1" value="' . route('admin-brand-featured', ['id1' => $data->id, 'id2' => 1]) . '" ' . $s . '>' . __('Yes') . '</option><option data-val="0" value="' . route('admin-brand-featured', ['id1' => $data->id, 'id2' => 0]) . '" ' . $ns . '>' . __('No') . '</option></select></div>';
            })
            ->addColumn('action', function (Brand $data) {
                return '<div class="action-list"><a data-href="' . route('admin-brand-edit', $data->id) . '" class="edit" data-toggle="modal" data-target="#modal1"> <i class="fas fa-edit"></i>' . __('Edit') . '</a><a href="javascript:;" data-href="' . route('admin-brand-delete', $data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i></a></div>';
            })
            ->rawColumns(['logo', 'is_featured', 'action'])
            ->toJson();
    }

    public function index()
    {
        return view('admin.brand.index');
    }

    public function create()
    {
        return view('admin.brand.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'slug' => 'required|unique:brands,slug|regex:/^[a-zA-Z0-9\s-]+$/',
            'image' => 'nullable|mimes:jpeg,jpg,png,svg,webp',
        ];
        $customs = [
            'slug.unique' => __('This slug has already been taken.'),
            'slug.regex' => __('Slug must not have special characters.'),
            'image.mimes' => __('Image type is invalid.'),
        ];
        $validator = Validator::make($request->all(), $rules, $customs);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        }

        $data = new Brand;
        $input = $request->only(['name', 'slug', 'is_featured']);
        $input['is_featured'] = $request->input('is_featured', 0) ? 1 : 0;

        $brandDir = public_path('assets/images/brands');
        if (! is_dir($brandDir)) {
            mkdir($brandDir, 0755, true);
        }

        if ($file = $request->file('image')) {
            $name = \PriceHelper::ImageCreateName($file);
            $file->move($brandDir, $name);
            $input['image'] = $name;
        }
        if ($file = $request->file('photo')) {
            $name = \PriceHelper::ImageCreateName($file);
            $file->move($brandDir, $name);
            $input['photo'] = $name;
        }

        $data->fill($input)->save();

        return response()->json(__('New Brand Added Successfully.'));
    }

    public function edit($id)
    {
        $data = Brand::findOrFail($id);

        return view('admin.brand.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|max:255',
            'slug' => 'required|unique:brands,slug,' . $id . '|regex:/^[a-zA-Z0-9\s-]+$/',
            'image' => 'nullable|mimes:jpeg,jpg,png,svg,webp',
            'photo' => 'nullable|mimes:jpeg,jpg,png,svg,webp',
        ];
        $customs = [
            'slug.unique' => __('This slug has already been taken.'),
            'slug.regex' => __('Slug must not have special characters.'),
        ];
        $validator = Validator::make($request->all(), $rules, $customs);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        }

        $data = Brand::findOrFail($id);
        $input = $request->only(['name', 'slug']);
        $input['is_featured'] = $request->input('is_featured', 0) ? 1 : 0;

        $brandDir = public_path('assets/images/brands');
        if (! is_dir($brandDir)) {
            mkdir($brandDir, 0755, true);
        }

        if ($file = $request->file('image')) {
            $name = \PriceHelper::ImageCreateName($file);
            $file->move($brandDir, $name);
            if ($data->image && file_exists($brandDir . DIRECTORY_SEPARATOR . $data->image)) {
                @unlink($brandDir . DIRECTORY_SEPARATOR . $data->image);
            }
            $input['image'] = $name;
        }
        if ($file = $request->file('photo')) {
            $name = \PriceHelper::ImageCreateName($file);
            $file->move($brandDir, $name);
            if ($data->photo && file_exists($brandDir . DIRECTORY_SEPARATOR . $data->photo)) {
                @unlink($brandDir . DIRECTORY_SEPARATOR . $data->photo);
            }
            $input['photo'] = $name;
        }

        $data->update($input);

        return response()->json(__('Brand Updated Successfully.'));
    }

    public function featured($id1, $id2)
    {
        $data = Brand::findOrFail($id1);
        $data->is_featured = (int) $id2;
        $data->save();

        return response()->json(__('Status Updated Successfully.'));
    }

    public function destroy($id)
    {
        $data = Brand::findOrFail($id);

        if (Product::where('brand_id', $id)->exists()) {
            return response()->json(__('Remove or reassign products using this brand first.'));
        }

        $brandDir = public_path('assets/images/brands');
        foreach (['image', 'photo'] as $field) {
            if ($data->{$field} && file_exists($brandDir . DIRECTORY_SEPARATOR . $data->{$field})) {
                @unlink($brandDir . DIRECTORY_SEPARATOR . $data->{$field});
            }
        }

        $data->delete();

        return response()->json(__('Brand Deleted Successfully.'));
    }
}
