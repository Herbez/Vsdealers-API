<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
    * @OA\Get(
    *      path="/api/category/show",
    *      operationId="categoryIndex",
    *      tags={"Categories"},
    *      summary="Display all categories",
    *      description="List of categories",
    *      @OA\Response(
    *          response="200",
    *          description="Success",
    *       ),
    *     )
    */
    public function index()
    {
        //
        $categories  =   Category::all();
        return response(['Number of Categories'=>count($categories) ,'category_list'=>$categories],200);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
    * @OA\Post(
    *      path="/api/category/store",
    *      operationId="categoryStore",
    *      tags={"Categories"},
    *      summary="Create a product category",
    *      description="Category creation",
    *     @OA\RequestBody(
    *          required=true,
    *          description="Request body for creating product category",
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *          @OA\Property(property="name", type="string"),       
    *          @OA\Property(property="image",type="string",format="binary"),
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048|unique:categories,image',
        ], [
            'name.required'=>'The name field is required.',
            'name.string'=>'The name must be a character not number.',
            'name.unique'=>'The name has already been taken.',
            'image.required'=>'The image field is required.',
            'image.mimes'=>'The image format is not valid.',
            'image.unique'=>'The image can not be duplicate.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }
        
        $productCategory = new Category;
        $productCategory->name = $request->name;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->extension();
            $image->move(public_path('category-images'), $imageName);
            $productCategory->image = $imageName;
        }

        $productCategory->save();

        return response()->json(['message' => 'Product category created successfully', 'category' => $productCategory], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

     

    public function update(Request $request, string $id)
    {
        //
        $validator = Validator::make($request->all(), [
            'name'=>'required|string|unique:categories,name',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048|unique:categories,image',
        ], [
            'name.required'=>'The name field is required.',
            'name.string'=>'The name must be a character not number.',
            'name.unique'=>'The name has already been taken.',
            'image.required'=>'The image field is required.',
            'image.mimes'=>'The image format is not valid.',
            'image.unique'=>'The image can not be duplicate.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }
        
        $category = Category::find($id);
        if(!$category){
            return response()->json(['message' => 'Category not found'], 404);
        }
           
        $category->name = $request->input('name');
        // Handle image upload
        if ($request->hasFile('image')) {
            //delete old image
            Storage::delete($category->image);

            $image = $request->file('image');
            $imageName = time().'.'.$image->extension();
            $image->move(public_path('product-category-images'), $imageName);
            $category->image = $imageName;
        }

        $category->save();
             return response()->json(['message' => 'Category updated successfully','category'=>$category], 200); 

        
    }
    /**
     * Remove the specified resource from storage.
     */

      /**
    * @OA\Delete(
    *      path="/api/category/delete/{id}'",
    *      operationId="categoryDelete",
    *      tags={"Categories"},
    *      summary="Delete Category by Id",
    *      description="Delete Category",
    *      @OA\Parameter(
    *          description="ID of category to delete",
    *          in="path",
    *          name="id",
    *          required=true,
    *          @OA\Schema(type="integer"),
    *      ),
    *      @OA\Response(
    *          response="204",
    *          description="Category deleted successfully",
    *       ),
    *      @OA\Response(
    *          response="404",
    *          description="Category not found",
    *       ),
    *     )
    */
    public function destroy(string $id)
    {
        //
        if(Category::where('id',$id)->exists()){
            Category::find($id)->delete();
            return response()->json([ 
                'message'=> 'Category Deleted Successfully',
            ], 200);
            }else{
                return response()->json([
                    'message'=> 'Category Not Found',
                ], 404);
            }
    }
}
