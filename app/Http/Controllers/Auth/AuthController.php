<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /**
    * @OA\Post(
    *      path="/api/tokens/register",
    *      operationId="userRegister",
    *      tags={"Users"},
    *      summary="Create a new User",
    *      description="User registration",
    *      @OA\RequestBody(
    *          required=true,
    *          description="Request body for User Registration",
    *          @OA\JsonContent(
    *          required={"name", "email", "password"},
    *          @OA\Property(property="name", type="string", example="example"),
    *          @OA\Property(property="email", type="string", example="example@gmail.com"),
    *          @OA\Property(property="password", type="string", example="example123"),
    *          @OA\Property(property="password_confirmation", type="string", example="example123")
    *        )
    *      ),
    *      @OA\Response(
    *          response="201",
    *          description="Success",
    *       ),
    *     )
    */
    function register(Request $request){

        // $this->validate($request,[
        //     'name'=>'required',
        //     'email'=>'required|email|unique:users,email',
        //     'password'=>'required|min:6'
        // ]);

        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6|confirmed'
        ], [
            'name.required'=>'The name field is required.',
            'email.required'=>'The email field is required.',
            'email.email'=>'The email must be a valid email address.',
            'email.unique'=>'The email has already been taken.',
            'password.required'=>'The password field is required.',
            'password.min'=>'The password must be at least 6 characters.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        $user   =   User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);

        return response(['message'=>'User Created','user'=>$user],201);




    }


    /**
    * @OA\Post(
    *      path="/api/tokens/login",
    *      operationId="userLogin",
    *      tags={"Users"},
    *      summary="User login",
    *      description="User login Authentication",
    *      @OA\RequestBody(
    *          required=true,
    *          description="Request body for User login",
    *          @OA\JsonContent(
    *          required={"email", "password"},
    *          @OA\Property(property="email", type="string", example="example@gmail.com"),
    *          @OA\Property(property="password", type="string", example="example123")
    *           )
    *      ),
    *      @OA\Response(
    *          response="201",
    *          description="Success",
    *       ),
    *      @OA\Response(
    *          response="404",
    *          description="User not found",
    *       ),
    *     )
    */
    function login(Request $request){
        $this->validate($request,[
            'email'=>'required|email',
            'password'=>'required|min:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if (Hash::check($request->password ,$user->password)) {
                $token  =   $user->createToken($user->name)->plainTextToken;
                return response(['message'=>'user login successfully','user'=>$user,'token'=>$token],200);
            }
            else{
                return response(['message'=>'Invalid Credentials'],403);
            }
        } else {
            return response(['message'=>'User not found'],403);
        }


    }

    /**
    * @OA\Post(
    *      path="/api/tokens/logout",
    *      operationId="userLogout",
    *      tags={"Users"},
    *      summary="User Logout",
    *      description="User logout Authentication",
    *      @OA\Response(
    *          response="200",
    *          description="Logout successfully",
    *       ),
    *     )
    */

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout successfully']);
    }

    /**
    * @OA\Get(
    *      path="/api/tokens/users",
     *      operationId="userList",
    *      tags={"Users"},
    *      summary="Display all users",
    *      description="List of users",
    *      @OA\Response(
    *          response="200",
    *          description="Success",
    *       ),
    *     )
    */

    function index(){
        $users  =   User::all();
        return response(['Number of Users'=>count($users),'user_list'=>$users],200);
    }
    /**
    * @OA\Delete(
    *      path="/api/tokens/delete/{id}'",
    *      operationId="userDestroy",
    *      tags={"Users"},
    *      summary="Delete User",
    *      description="Delete User",
    *      @OA\Parameter(
    *          description="ID of user to delete",
    *          in="path",
    *          name="id",
    *          required=true,
    *          @OA\Schema(type="integer"),
    *      ),
    *      @OA\Response(
    *          response="204",
    *          description="User deleted successfully",
    *       ),
    *      @OA\Response(
    *          response="404",
    *          description="User not found",
    *       ),
    *     )
    */
    function destroy($id){
        if(User::where('id',$id)->exists()){
            User::find($id)->delete();
            return response()->json([
                'message'=> 'User Deleted Successfully',
            ], 200);
            }else{
                return response()->json([
                    'message'=> 'User Not Found',
                ], 404);
            }
    }

    /**
    * @OA\Put(
    *      path="/api/tokens/update/{id}",
    *      operationId="userUpdate",
    *      tags={"Users"},
    *      summary="Update a user by Id",
    *      description="Update a user record by Id",
    *      @OA\Parameter(
    *          description="Id of user to Update",
    *          in="path",
    *          name="id",
    *          required=true,
    *          @OA\Schema(type="integer"),
    *      ),
    *      @OA\RequestBody(
    *          required=true,
    *          @OA\JsonContent(required={"name", "email", "password"})
    *      ),
    *      @OA\Response(
    *          response="201",
    *          description="User Updated Successfully",
    *       ),
    *      @OA\Response(
    *          response="404",
    *          description="User not found",
    *       ),
    *     )
    */
    function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'email'=>'required|email|',
            'password'=>'required|min:6'
        ], [
            'name.required'=>'The name field is required.',
            'email.required'=>'The email field is required.',
            'email.email'=>'The email must be a valid email address.',
            'password.required'=>'The password field is required.',
            'password.min'=>'The password must be at least 6 characters.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        $user = User::find($id);
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        $user->save();
             return response()->json(['message' => 'User updated successfully','user'=>$user], 200);

    }

}
