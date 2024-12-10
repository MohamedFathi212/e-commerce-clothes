<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

    public function brand_delete($id)
    {
        $brand = Brand::find($id);

        if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
            File::delete(public_path('uploads/brands') . '/' . $brand->image);
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

    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }


    public function category_update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $category = Category::findOrFail($request->id);

            // تحديث الحقول
            $category->name = $request->name;
            $category->slug = $request->slug;

            // تحديث الصورة إذا تم رفعها
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/categories'), $imageName);

                // حذف الصورة القديمة إذا كانت موجودة
                if ($category->image && file_exists(public_path('uploads/categories/' . $category->image))) {
                    unlink(public_path('uploads/categories/' . $category->image));
                }

                $category->image = $imageName;
            }

            $category->save();

            return redirect()->route('admin.categories')->with('status', 'Category updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function category_delete($id)
    {
        $category = Category::find($id);

        if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
            File::delete(public_path('uploads/categories') . '/' . $category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category has been Deleted successfully!');
    }

    public function products()
    {
        $products = Product::orderBy('created_at' ,'DESC')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function product_add()
    {
        $categories =  Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-add', compact('categories', 'brands'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required', 
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',	
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required', 
            'stock_status' => 'required', 	
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
            'images.*' => 'nullable|mimes:png,jpg,jpeg,gif|max:2048', // تحقق من صور المعرض
        ]);
    
        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description; 
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
    
        if ($request->hasFile('image')) {
            $originalName = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = Str::slug($originalName) . '-' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/products/thumbnails'), $fileName);
            $product->image = $fileName;
        }
    
        $galleryArr = [];
        if ($request->hasFile('images')) {
            $allowedFileExtensions = ['jpg', 'png', 'jpeg', 'gif'];
            foreach ($request->file('images') as $file) {
                $extension = $file->getClientOriginalExtension();
                if (in_array($extension, $allowedFileExtensions)) {
                    $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $extension;
                    $file->move(public_path('uploads/products/thumbnails'), $fileName);
                    $galleryArr[] = $fileName;
                }
            }
        }
        $product->images = implode(',', $galleryArr);
        $product->save();
    
        return redirect()->route('admin.products')->with('status', 'Product has been added successfully');
    }

    public function product_edit($id)
    {
        $product = Product::find($id);
        $categories =  Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-edit', compact('product','categories','brands'));
        
    }

    public function product_update(Request $request )
    {
        $request->validate([
            'name' => 'required', 
            'slug' => 'required|unique:products,slug,' . $request->id,
            'short_description' => 'required',
            'description' => 'required',	
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required', 
            'stock_status' => 'required', 	
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
            'images.*' => 'nullable|mimes:png,jpg,jpeg,gif|max:2048', // تحقق من صور المعرض
        ]);

        $product = Product::find($request->id);
        
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description; 
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        if ($request->hasFile('image')) {
            $originalName = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = Str::slug($originalName) . '-' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/products/thumbnails'), $fileName);
            $product->image = $fileName;
        }
    
        $galleryArr = [];
        if ($request->hasFile('images')) {
            $allowedFileExtensions = ['jpg', 'png', 'jpeg', 'gif'];
            foreach ($request->file('images') as $file) {
                $extension = $file->getClientOriginalExtension();
                if (in_array($extension, $allowedFileExtensions)) {
                    $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $extension;
                    $file->move(public_path('uploads/products/thumbnails'), $fileName);
                    $galleryArr[] = $fileName;
                }
            }
        }
        $product->images = implode(',', $galleryArr);
        $product->save();
    
        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully');
    
    }

    public function product_delete($id)
    {
        $product = Product::find($id);

        if (File::exists(public_path('uploads/products/thumbnails') . '/' . $product->image)) {
            File::delete(public_path('uploads/products/thumbnails') . '/' . $product->image);
        }
        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product has been Deleted successfully!');
    }

}
