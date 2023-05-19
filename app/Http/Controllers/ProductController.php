<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

        /**
    * @OA\Get(
    *      path="/api/product/show",
    *      operationId="productIndex",
    *      tags={"Products"},
    *      summary="Display all products",
    *      description="List of products",
    *      @OA\Response(
    *          response="200",
    *          description="Success",
    *       ),
    *     )
    */
    public function index()
    {
        //
        $products=Product::all();
        return response()->json([
            'Number of Products'=>count($products) ,
            'product_list'=>$products,

        ],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

        /**
    * @OA\Post(
    *      path="/api/product/store",
    *      operationId="productStore",
    *      tags={"Products"},
    *      summary="Create a product product",
    *      description="Product creation",
    *     @OA\RequestBody(
    *          required=true,
    *          description="Request body for creating product product",
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *          @OA\Property(property="name", type="string"),
    *          @OA\Property(property="description", type="text"),
    *          @OA\Property(property="price", type="decimal"),
    *          @OA\Property(property="image",type="string"),
    *          @OA\Property(property="type", type="integer"),
    *          @OA\Property(property="quantity", type="string"),
    *              )
    *          )
    *     ),
    *      @OA\Response(
    *      response="201",
    *      description="Success",
    *      ),
    *      @OA\Response(
    *          response="422",
    *          description="Validation error"
    *      ),
    *     )
    */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name'=>'required|string|unique:categories,name',
            'description' =>'required',
            'price' =>'required',
            'image' => 'required',
            'type' =>'required',
            'quantity' =>'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        $products= new Product;
        $products->name = $request->name;
        $products->description = $request->description ;
        $products->price = $request->price ;
        $products->image = $request->image ;
        $products->type = $request->type ;
        $products->quantity = $request->quantity ;
        $products->save();

        return response()->json(['message' => 'Product created successfully', 'Product' => $products], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    /**
    * @OA\Put(
    *      path="/api/product/update/{id}",
    *      operationId="productUpdate",
    *      tags={"Products"},
    *      summary="Update Product",
    *      description="Update record of a Product ",
    *      @OA\Parameter(
    *          description="Parameter with example",
    *          in="path",
    *          name="id",
    *          required=true,
    *          @OA\Schema(type="integer"),
    *      ),
    *     @OA\RequestBody(
    *          required=true,
    *          @OA\JsonContent(),
    *          description="Request body for updating product",
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *          @OA\Property(property="name", type="string"),
    *          @OA\Property(property="description", type="text"),
    *          @OA\Property(property="price", type="decimal"),
    *          @OA\Property(property="image", type="string"),
    *          @OA\Property(property="type", type="string"),
    *          @OA\Property(property="quantity", type="string"),
    *              )
    *          )
    *     ),
    *      @OA\Response(
    *      response="200",
    *      description="Success",
    *      ),
    *      @OA\Response(
    *          response="404",
    *          description="Product not found"
    *      ),
    *     )
    */
    public function update(Request $request, $id)
    {
        //
        $prod = Product::find($id);
        if(!$prod){
            return response()->json(['message' => 'Product not found'], 404);
        }
        $prod->name = $request->name;
        $prod->description = $request->description;
        $prod->price = $request->price;
        $prod->type = $request->type;
        $prod->image = $request->image;
        $prod->quantity = $request->quantity;
        $prod->save();
            return response()->json([
            'message' => 'Product updated successfully',
            'Product'=>$prod], 200);
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
    * @OA\Delete(
    *      path="/api/product/delete/{id}'",
    *      operationId="productDelete",
    *      tags={"Products"},
    *      summary="Delete product by Id",
    *      description="Delete product",
    *      @OA\Parameter(
    *          description="ID of product to delete",
    *          in="path",
    *          name="id",
    *          required=true,
    *          @OA\Schema(type="integer"),
    *      ),
    *      @OA\Response(
    *          response="204",
    *          description="product deleted successfully",
    *       ),
    *      @OA\Response(
    *          response="404",
    *          description="product not found",
    *       ),
    *     )
    */
    public function destroy(string $id)
    {
        //
        if(Product::where('id',$id)->exists()){
            Product::find($id)->delete();
            return response()->json([
                'message'=> 'Product Deleted Successfully',
            ], 200);
            }else{
                return response()->json([
                    'message'=> 'Product Not Found',
                ], 404);
            }
    }
    /**
     * Display the specified resource.
     */

    /**
    * @OA\Get(
    *      path="/api/product/search/{name}",
    *      operationId="productSearch",
    *      tags={"Products"},
    *      summary="Search product",
    *      description="Search product by name",
    *      @OA\Parameter(
    *          description="Name of product to search for",
    *          in="path",
    *          name="name",
    *          required=true,
    *          @OA\Schema(type="string"),
    *      ),
    *      @OA\Response(
    *          response="200",
    *          description="success",
    *       ),
    *      @OA\Response(
    *          response="404",
    *          description="product not found",
    *       ),
    *     )
    */


    public function search($name)
    {
        $results = Product::where('name', 'like', '%' . $name . '%')->get();
        return response()->json(['Product' => $results], 200);

    }
}
