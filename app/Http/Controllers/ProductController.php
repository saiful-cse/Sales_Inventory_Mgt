<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function productCreate(Request $request)
    {
        $user_id = $request->header('user_id');

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'unit' => 'required|string|max:50',
            'category_id' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'price' => $request->price,
            'unit' => $request->unit,
            'category_id' => $request->category_id,
            'user_id' => $user_id
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = 'uploads/' . $fileName;
            $file->move(public_path('uploads'), $fileName);
            $data['image'] = $filePath;
        }

        Product::create($data);

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Product created successfully'
        // ]);
        $data = ['status' => true, 'message' => 'Product created successfully', 'error' => ''];
        return redirect('/product_page')->with($data);
    }

    public function productList(Request $request)
    {
        $user_id = $request->header('id');

        $products = Product::where('user_id', $user_id)->get();
        return $products;
    }

    public function productById(Request $request)
    {
        $user_id = $request->header('id');
        return Product::where('user_id', $user_id)->where('id', $request->input('id'))->get();
    }

    public function productUpdate(Request $request)
    {
        $user_id = $request->header('user_id');
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'unit' => 'required|string|max:50',
            'category_id' => 'required|integer'
        ]);

        $product = Product::where('user_id', $user_id)->findOrFail($request->input('id'));
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->unit = $request->input('unit');
        $product->category_id = $request->input('category_id');

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }
            $request->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = 'uploads/' . $fileName;
            $file->move(public_path('uploads'), $fileName);
            $product->image = $filePath;
        }
        $product->save();

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Product updated successfull'
        // ]);
        $data = ['status' => true, 'message' => 'Product updated successfully', 'error' => ''];
        return redirect('/product_page')->with($data);
    }

    public function productDelete(Request $request, $id)
    {
        try {
            $user_id = $request->header('user_id');

            $product = Product::where('user_id', $user_id)->findOrFail($id);

            if ($product->image && public_path($product->image)) {
                unlink(public_path($product->image));
            }
            $product->delete();

            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Product deleted successfull'
            // ]);
            $data = ['status' => true, 'message' => 'Product deleted successfully', 'error' => ''];
            return redirect()->back()->with($data);
        } catch (\Throwable $th) {
            // return response()->json([
            //     'status' => 'failed',
            //     'message' => 'Something went wrong try again later'
            // ]);
            $data = ['status' => false, 'message' => 'Something went wrong try again later', 'error' => ''];
            return redirect()->back()->with($data);
        }
    }

    public function ProductPage(Request $request)
    {
        $user_id = request()->header('user_id');
        $products = Product::where('user_id', $user_id)->with('category')->latest()->get();

        return Inertia::render('ProductPage', ['products' => $products]);
    }

    public function ProductSavePage(Request $request)
    {
        $user_id = $request->header('user_id');
        $categories = Category::where('user_id', $user_id)->get();
        $product  = Product::where('user_id', $user_id)->where('id', $request->query('id'))->first();

        return Inertia::render('ProductSavePage', ['categories' => $categories, 'product' => $product]);
    }
}
