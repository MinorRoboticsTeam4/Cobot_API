<?php

namespace App\Http\Controllers;

use App\Cobot\Transformers\OrderTransformer;
use App\Cobot\Transformers\ProductTransformer;
use App\Order;
use App\Product;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrdersController extends ApiController
{

    protected $robot_id = 2;
    protected $delivered_status = 3;
    protected $orderTransformer;
    protected $productTransformer;


    /**
     * UsersController constructor.
     * @param OrderTransformer $orderTransformer
     * @param ProductTransformer $productTransformer
     */
    public function __construct(OrderTransformer $orderTransformer, ProductTransformer $productTransformer)
    {
        $this->orderTransformer = $orderTransformer;
        $this->productTransformer = $productTransformer;

        $this->middleware('auth.basic.once');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if( Auth::user()->id != $this->robot_id)
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $order = Order::where('delivery_status', '<', '3')->first();

        if ( !$order )
        {
            return $this->respondNotFound('Orders do not exist.');
        }

        return $this->respond($this->orderTransformer->transformForRobot($order));
    }

    /**
     * Returns the amount of uncompleted orders
     *
     * @param null $order_id
     * @return \Illuminate\Http\Response
     */
    public function count($order_id = NULL)
    {
        if($order_id) {
            $orderCount = Order::where('delivery_status', '<', '3')->where('id', '<=', $order_id)->count();
        } else {
            $orderCount = Order::where('delivery_status', '<', '3')->count();
        }

        if ( !$orderCount )
        {
            return $this->respondNotFound('No orders exist.');
        }

        return $this->respond(["order_count" => $orderCount]);
    }

    /**
     * Returns the amount of uncompleted orders
     *
     * @return \Illuminate\Http\Response
     */
    public function nfc($nfc_id)
    {
        if( Auth::user()->id != $this->robot_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }
        $user = User::where('nfc_identifier', $nfc_id)->first();

        if ( !$user )
        {
            return $this->respondNotFound('No user found with this nfc_id');
        }

        $product = Product::find($user->product_id);

        if ( !$product )
        {
            return $this->respondNotFound('User has no nfc product selected');
        }

        return $this->respond($this->productTransformer->transform($product));
    }

    /**
     * Display the specified resource.
     *
     * @param $order_id
     * @return \Illuminate\Http\Response
     */
    public function show($order_id)
    {
        if( Auth::user()->id != $this->robot_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $order = Order::find($order_id);
        if ( !$order )
        {
            return $this->respondNotFound('Order does not exist.');
        }

        return $this->respond( $this->orderTransformer->transform($order));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $order_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_id)
    {
        if( Auth::user()->id != $this->robot_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $validator = Validator::make($request->all(), [
            'delivery_status' => 'integer'
        ]);

        if($request['delivery_status'] == $this->delivered_status)
        {
            $request['delivered_at'] = date( 'Y-m-d H:i:s', time() );
        }

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors(), "Parameters failed validation for an order");
        }

        $order = Order::find($order_id);
        if ( !$order )
        {
            return $this->respondNotFound('Order does not exist.');
        }

        $order->update($request->all());
        return $this->respondUpdated("Order successfully updated.");
    }
}
