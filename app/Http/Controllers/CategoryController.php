<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function CategoryCreate(Request $request)
    {
        $user_id = $request->header('user_id');

        Category::create([
            'name' => $request->name,
            'user_id' => $user_id
        ]);

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Category created successfull'
        // ]);
        $data = ['message' => 'Category created successfull', 'status' => true, 'error' => ''];
        return redirect('category_page')->with($data);
    }

    public function CategoryList(Request $request)
    {
        $user_id = $request->header('id');

        $categories = Category::where('user_id', $user_id)->get();
        return $categories;
    }

    public function CategoryById(Request $request)
    {
        $user_id = $request->header('id');

        $category = Category::where('user_id', $user_id)->where('id', $request->input('id'))->first();
        return $category;
    }

    public function CategoryUpdate(Request $request)
    {
        $user_id = $request->header('user_id');

        Category::where('user_id', $user_id)->where('id', $request->input('id'))->update([
            'name' => $request->name
        ]);

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Category name updated sucessfull'
        // ]);
        $data = ['message' => 'Category name updated sucessfull', 'status' => true, 'error' => ''];
        return redirect('category_page')->with($data);
    }

    public function CategoryDelete(Request $request, $id)
    {
        $user_id = $request->header('user_id');

        Category::where('user_id', $user_id)->where('id', $id)->delete();

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Category name deleted sucessfull'
        // ]);
        $data = ['message' => 'Category name deleted sucessfull', 'status' => true, 'error' => ''];
        return redirect('category_page')->with($data);
    }

    public function CategoryPage(Request $request)
    {
        $user_id = $request->header('user_id');

        $categories = Category::where('user_id', $user_id)->latest()->get();

        return Inertia::render('CategoryPage', ['categories' => $categories]);
    }

    public function CategorySavePage(Request $request)
    {
        $category_id = $request->query('id');
        $user_id = $request->header('user_id');

        $category = Category::where('user_id', $user_id)->where('id', $category_id)->first();

        return Inertia::render('CategorySavePage', ['category' => $category]);
    }
}
