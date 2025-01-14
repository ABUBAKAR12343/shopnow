<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('category')->get();

        if ($products) {
            return response()->json([
                'success' => 1,
                'products' => $products
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'products' => [],
                'message'    => 'No Product Found!'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:products,name',
                "code" => 'required|unique:product,code',
                "status" => "required",
                "slug" => "required",
                "meta_title" => "required",
                "meta_desc" => "required",
                "meta_keywords" => "required",
                // "banner_file" => "required|image",
                "description" => "required",
                "category_type" => "required",
                "file" => "required",
                "price" => "required",
                "is_featured" => "required"
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'error' => $validator->errors()->all()
            ], 422);
        }

        if ($request->has('file')) {
            $file = $request->file;
            $fileName = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('productsImages'), $fileName);
        }


        $product = new Product();
        $product->name = $request->name;
        $product->code = $request->code;
        $product->status = $request->status;
        $product->slug = $request->slug;
        $product->meta_title = $request->meta_title;
        $product->meta_keywords = $request->meta_keywords;
        $product->meta_desc = $request->meta_desc;
        $product->description = $request->description;
        $product->category_type = $request->category_type;
        $product->file = $fileName;
        $product->price = $request->price;
        $product->is_featured = $request->is_featured;
        $product->discount = $request->discount ?? 0;

        if ($product->save()) {
            return response()->json([
                'success' => 1,
                'message'    => 'Product Created Successfully!'
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'message'    => 'Error While Creating Product!'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $product = Product::where('_id', $id)->first();

        if ($product) {
            $del_product = $product->delete();
            if ($del_product) {
                return response()->json([
                    'success' => 1,
                    'message'   => 'Product Deleted Successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => 0,
                    'message'   => 'Error While Deleting Product!'
                ]);
            }
        } else {
            return response()->json([
                'success' => 0,
                'message'   => 'Product Not Found!'
            ]);
        }
    }
}
