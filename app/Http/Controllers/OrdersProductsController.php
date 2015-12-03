<?php namespace App\Http\Controllers;

use App\Cobot\Transformers\OrderTransformer;
use App\Cobot\Transformers\ProductTransformer;
use App\Order;
use App\OrderProduct;
use Illuminate\Http\Request;
use App\Cobot\Transformers\UserTransformer;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Class UsersProductsController
 * @package App\Http\Controllers
 */
class OrdersProductsController extends ApiController
{

    /**
     * @var UserTransformer
     */
    protected $orderTransformer;
    /**
     * @var ProductTransformer
     */
    protected $productTransformer;

    /**
     * UsersProductsController constructor.
     * @param OrderTransformer $orderTransformer
     * @param ProductTransformer $productTransformer
     */
    public function __construct(OrderTransformer $orderTransformer, ProductTransformer $productTransformer)
    {
        $this->userTransformer = $orderTransformer;
        $this->productTransformer = $productTransformer;

        $this->middleware('auth.basic.once');
    }

    /**
     * Display a listing of the resource.
     *
     * @param $order_id
     * @return \Illuminate\Http\Response
     */
    public function index($order_id)
    {
        $order = Order::find($order_id);
        if ( !$order )
        {
            return $this->respondNotFound('Order does not exist.');
        }

        if( Auth::user()->id != $order->user->id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        return $this->respond($this->productTransformer->transformCollection($order->products->all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $order_id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $order_id)
    {
        $order = Order::find($order_id);
        if ( !$order )
        {
            return $this->respondNotFound('Order does not exist.');
        }

        if( Auth::user()->id != $order->user->id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $request['order_id'] = $order_id;
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer',
            'name' => 'required|string',
            'type' => 'required|integer',
            'image_path' => 'required|string',
            'option_strength' => 'required|integer',
            'option_milk' => 'required|integer',
            'option_sugar' => 'required|integer',
            'option_mug'  => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors(), "Parameters failed validation for a product");
        }

        $orderProduct = OrderProduct::create($request->all());
        return $this->respondCreated($this->productTransformer->transform($orderProduct));
    }

    /**
     * Display the specified resource.
     *
     * @param $order_id
     * @param $product_id
     * @return \Illuminate\Http\Response
     */
    public function show($order_id, $product_id)
    {
        $order = Order::find($order_id);
        if ( !$order )
        {
            return $this->respondNotFound('Order does not exist.');
        }

        if( Auth::user()->id != $order->user->id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $product = $order->products->find($product_id);
        if ( !$product )
        {
            return $this->respondNotFound('Order product does not exist.');
        }

        return $this->respond([
            'data' => $this->productTransformer->transform($product)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $order_id
     * @param $product_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_id, $product_id)
    {
        $order = Order::find($order_id);
        if ( !$order )
        {
            return $this->respondNotFound('Order does not exist.');
        }

        if( Auth::user()->id != $order->user->id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $request['order_id'] = $order_id;
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer',
            'name' => 'string',
            'type' => 'integer',
            'image_path' => 'string',
            'option_strength' => 'integer',
            'option_milk' => 'integer',
            'option_sugar' => 'integer',
            'option_mug'  => 'boolean'
        ]);

        if ($validator->fails())
        {
            return $this->respondValidationError($validator->errors(), "Parameters failed validation for a product");
        }

        $product = $order->products->find($product_id);
        if ( !$product )
        {
            return $this->respondNotFound('Order product does not exist.');
        }

        $product->update($request->all());
        return $this->respondUpdated("Order product successfully updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $order_id
     * @param $product_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($order_id, $product_id)
    {
        $order = Order::find($order_id);
        if ( !$order )
        {
            return $this->respondNotFound('Order does not exist.');
        }

        if( Auth::user()->id != $order->user->id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $product = $order->products->find($product_id);
        if ( !$product )
        {
            return $this->respondNotFound('Order product does not exist.');
        }

        $product->delete();
        return $this->respondUpdated("Order product successfully deleted.");

    }

}
