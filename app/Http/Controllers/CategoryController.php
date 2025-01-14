<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
// use phpseclib3\Crypt\RC2;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name',
            'code' => 'required|unique:categories,code',
            'status' => 'required',
            'file' => 'required|image',
            'slug' => 'required',
            'meta_title' => 'required',
            'meta_desc' => 'required',
            'meta_keywords' => 'required',
            'banner_file' => 'required|image',
            'category_type' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'error'   => $validator->errors()->all()
            ], 422);
        }

        $validated = $validator->validated();

        if ($request->has('file')) {
            $file = $request->file;
            $fileName = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('categoryImages'), $fileName);
        }

        if ($request->hasFile('banner_file')) {
            $bannerFile = $request->file('banner_file');
            $bannerFileName = time() . '-banner-' . $bannerFile->getClientOriginalName();
            $bannerFile->move(public_path('uploads'), $bannerFileName);
        }

        $category = new Category();
        $category->name = $validated['name'];
        $category->code = $validated['code'];
        $category->file =  $fileName;
        $category->status = $validated['status'];
        $category->slug = $validated['slug'];
        $category->meta_title = $validated['meta_title'];
        $category->meta_desc = $validated['meta_desc'];
        $category->meta_keywords = $validated['meta_keywords'];
        $category->banner_file = $bannerFileName;
        $category->category_type = $validated['category_type'];
        $category->description = $validated['description'];

        if ($category->save()) {
            return response()->json([
                'success'   => 1,
                'message'   => 'Category Created Successfully!'
            ]);
        } else {
            return response()->json([
                'success'   => 0,
                'message'   => 'Error While Creating Category!'
            ]);
        }
    }


    public function index(Request $request)
    {
        $categories = Category::all();

        if ($categories) {
            return response()->json([
                'success' => 1,
                'categories' => $categories
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'categories' => [],
                'message'    => 'No Category Found!'
            ]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;

        $category = Category::where('_id', $id)->first();

        if ($category) {
            $del_user = $category->delete();
            if ($del_user) {
                return response()->json([
                    'success' => 1,
                    'message'   => 'Category Deleted Successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => 0,
                    'error'   => 'Error While Deleting Category!'
                ]);
            }
        } else {
            return response()->json([
                'success' => 0,
                'error'   => 'Category Not Found!'
            ]);
        }
    }

    public function edit($id)
    {
        $category = Category::where('_id', $id)->first();

        if ($category) {
            return response()->json([
                'success' => 1,
                'category'    => $category,
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'error'    => 'category Not Found!',
            ]);
        }
    }


    public function update(Request $request)
    {
        $id = $request->id;
        $category = Category::where('_id', $id)->first();

        if (!$category) {
            return response()->json([
                'success' => 0,
                'message' => 'Category Not Found!',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'unique:categories,name,' . $id . ',_id'
            ],
            'code' => 'required|unique:categories,code,' . $id . ',_id',
            'status' => 'required',
            // 'file' => 'nullable|image',
            'slug' => 'required',
            'meta_title' => 'required',
            'meta_desc' => 'required',
            'meta_keywords' => 'required',
            // 'banner_file' => 'nullable|image',
            'category_type' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'error' => $validator->errors()->all()
            ], 422);
        }

        $data = $request->all();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('categoryImages'), $fileName);
            $data['file'] = $fileName;
        } else {
            unset($data['file']);
        }

        if ($request->hasFile('banner_file')) {
            $bannerFile = $request->file('banner_file');
            $bannerFileName = time() . '-banner-' . $bannerFile->getClientOriginalName();
            $bannerFile->move(public_path('uploads'), $bannerFileName);
            $data['banner_file'] = $bannerFileName;
        } else {
            unset($data['banner_file']);
        }

        $updated = $category->update([
            'name' => $data['name'],
            'status' => $data['status'],
            'code' => $data['code'],
            'file' => $data['file'] ?? $category->file,
            'slug' => $data['slug'],
            'meta_title' => $data['meta_title'],
            'meta_desc' => $data['meta_desc'],
            'meta_keywords' => $data['meta_keywords'],
            'banner_file' => $data['banner_file'] ?? $category->banner_file,
            'category_type' => $data['category_type'],
            'description' => $data['description'],
            'discount'  => $data['discount'] ?? 0
        ]);

        if ($updated) {
            return response()->json([
                'success' => 1,
                'message' => 'Category Updated Successfully!',
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'message' => 'Error While Updating Record!',
            ]);
        }
    }
}
