<?php

namespace App\Http\Controllers;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{

    public function index()
    {
        return view('admin.index');
    }

    public function brands()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    public function add_brand()
    {
        return view('admin.brand-add');
    }

    public function store_brand(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $file_name = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/brands'), $file_name);
            $brand->image = $file_name;
        }

        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand  has been added successfully!');
    }
}
