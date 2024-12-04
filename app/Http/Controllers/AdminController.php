<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
            // الحصول على الاسم الأصلي للملف
            $originalName = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
            // إزالة المسافات أو الأحرف غير المسموح بها
            $fileName = Str::slug($originalName) . '.' . $request->image->extension();
            // التأكد من أن الاسم فريد
            $fileName = uniqid() . '_' . $fileName;
            // نقل الملف إلى المسار
            $request->image->move(public_path('uploads/brands'), $fileName);
            $brand->image = $fileName;
        }

        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been added successfully!');
    }

    public function brand_edit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug,' . $request->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = $request->slug;

        // معالجة الصورة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إن وجدت
            if ($brand->image && file_exists(public_path('uploads/brands/' . $brand->image))) {
                unlink(public_path('uploads/brands/' . $brand->image));
            }

            // الحصول على الاسم الأصلي للملف
            $originalName = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
            // تنظيف الاسم
            $fileName = Str::slug($originalName) . '.' . $request->image->extension();
            // التأكد من أن الاسم فريد
            $fileName = uniqid() . '_' . $fileName;
            // نقل الملف إلى المسار
            $request->image->move(public_path('uploads/brands'), $fileName);
            $brand->image = $fileName;
        }

        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been updated successfully!');
    }

    public function brand_delete($id) {
        $brand = Brand::find($id);

        if(File::exists(public_path('uploads/brands').'/'.$brand->image))
        {
            File::delete(public_path('uploads/brands').'/'.$brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand has been Deleted successfully!');
    }

    public function categories()
    {
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.categories', compact('categories'));
    }


    public function category_add()
    {
        return view('admin.category-add');
    }


    public function category_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            // الحصول على الاسم الأصلي للملف
            $originalName = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
            // إزالة المسافات أو الأحرف غير المسموح بها
            $fileName = Str::slug($originalName) . '.' . $request->image->extension();
            // التأكد من أن الاسم فريد
            $fileName = uniqid() . '_' . $fileName;
            // نقل الملف إلى المسار
            $request->image->move(public_path('uploads/categories'), $fileName);
            $category->image = $fileName;
        }

        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been added successfully!');
    }



}
