<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    //

    /**
    * @OA\Get(
    *      path="/api/orders/list",
    *      operationId="orderList",
    *      tags={"Orders"},
    *      summary="Orders List",
    *      description="client Order's list",
    *      @OA\Response(
    *          response="200",
    *          description="Success",
    *       ),
    *     )
    */
    public function index(){
        $orders = Order::all();
        return response(['Number of orders'=>count($orders),'orders_list'=>$orders],200);
    }

    /**
    * @OA\Post(
    *      path="/api/orders/store",
    *      operationId="orderStore",
    *      tags={"Orders"},
    *      summary="Make Order",
    *      description="Create new order",
    *      @OA\RequestBody(
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *          @OA\Property(property="client_name", type="string"),
    *          @OA\Property(property="total_price", type="string"),
    *          @OA\Property(property="payment_mode", type="string"),
    *              )
    *          )
    *      ),
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
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'client_name'=>'required',
            'total_price'=>'required',
            'payment_mode'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        $orders   =   Order::create([
            'client_name'=>$request->client_name,
            'total_price'=>$request->total_price,
            'payment_mode'=>$request->payment_mode
        ]);

        return response(['message'=>'Order Created','orders'=>$orders],201);

    }
}
