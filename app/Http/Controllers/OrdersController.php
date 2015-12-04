<?php

namespace App\Http\Controllers;

use App\Cobot\Transformers\OrderTransformer;
use App\Order;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrdersController extends ApiController
{

    protected $robot_id = 2;
    protected $orderTransformer;

    /**
     * UsersController constructor.
     * @param OrderTransformer $orderTransformer
     */
    public function __construct(OrderTransformer $orderTransformer)
    {
        $this->orderTransformer = $orderTransformer;

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

        $orders = Order::where('delivered_at', null)->all();

        if ( count($orders) == 0 )
        {
            return $this->respondNotFound('Orders do not exist.');
        }

        return $this->respond([
            'data' => $this->orderTransformer->transformCollection($orders->all())
        ]);
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

        return $this->respond([
            'data' => $this->orderTransformer->transform($order)
        ]);
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
            'delivery_status' => 'integer',
            'delivered_at' => 'date'
        ]);

        if($request['delivered_at'])
        {
            $request['delivered_at'] = date( 'Y-m-d H:i:s', strtotime($request['delivered_at']) );
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
