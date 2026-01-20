<?php

namespace App\Http\Controllers\Admin;

use App\{
    Models\Category,
    Models\Subcategory,
    Models\Brand,
};
use Illuminate\Http\Request;
use Validator;
use Datatables;

class BrandsController extends AdminBaseController
{

   //*** GET Request
    public function load($id)
    {
        $brand = Brand::findOrFail($id);
        dd($brand);
        return view('load.brand',compact('brand'));
    }
}
