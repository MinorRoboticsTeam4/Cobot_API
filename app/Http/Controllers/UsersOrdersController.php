<?php namespace App\Http\Controllers;

use App\Cobot\Transformers\UserTransformer;
use App\Cobot\Transformers\OrderTransformer;
use App\Order;
use App\User;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UsersOrdersController extends ApiController
{

    /**
     * @var UserTransformer
     */
    protected $userTransformer;
    protected $orderTransformer;

    public function __construct(UserTransformer $userTransformer, OrderTransformer $orderTransformer)
    {
        $this->userTransformer = $userTransformer;
        $this->orderTransformer = $orderTransformer;

        $this->middleware('auth.basic.once');
    }

    /**
     * Display a listing of the resource.
     *
     * @param $user_id
     * @return \Illuminate\Http\Response
     */
    public function index($user_id)
    {
        if( Auth::user()->id != $user_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $user = User::find($user_id);
        if ( !$user )
        {
            return $this->respondNotFound('User does not exist.');
        }

        $order = $user->orders->where('delivery_status', '0')->all();
        if ( !$order )
        {
            return $this->respondNotFound('Orders do not exist.');
        }

        return $this->respond($this->orderTransformer->transformCollection($order));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $user_id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $user_id)
    {
        $user = Auth::user();
        if( $user->id != $user_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $request['user_id'] = $user->id;
        $request['location'] = $user->location;

        if(count($user->orders) != 0) {
            return $this->respondForbiddenRequest("User already has an open order");
        }

        $order = Order::create($request->all());
        return $this->respondCreated($this->orderTransformer->transform($order));
    }

    /**
     * Display the specified resource.
     *
     * @param $user_id
     * @param $order_id
     * @return \Illuminate\Http\Response
     */
    public function show($user_id, $order_id)
    {
        if( Auth::user()->id != $user_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $user = User::find($user_id);
        if ( !$user )
        {
            return $this->respondNotFound('User does not exist.');
        }

        $order = $user->orders->find($order_id);
        if ( !$order )
        {
            return $this->respondNotFound('Order does not exist.');
        }

        return $this->respond($this->orderTransformer->transform($order));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $user_id
     * @param $order_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id, $order_id)
    {
        if( Auth::user()->id != $user_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $request['user_id'] = $user_id;
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'location' => 'string',
            'delivered_at' => 'date',
            'delivery_status' => 'integer',
        ]);

        if($request['delivered_at'])
        {
            $request['delivered_at'] = date( 'Y-m-d H:i:s', strtotime($request['delivered_at']) );
        }

        if ($validator->fails())
        {
            return $this->respondValidationError($validator->errors(), "Parameters failed validation for a product");
        }

        $user = User::find($user_id);
        if ( !$user )
        {
            return $this->respondNotFound('User does not exist.');
        }

        $order = $user->orders->find($order_id);
        if ( !$order )
        {
            return $this->respondNotFound('Order does not exist.');
        }

        $order->update($request->all());
        return $this->respondUpdated("Order successfully updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $user_id
     * @param $order_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_id, $order_id)
    {
        if( Auth::user()->id != $user_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $user = User::find($user_id);
        if ( !$user )
        {
            return $this->respondNotFound('User does not exist.');
        }

        $order = $user->orders->find($order_id);
        if ( !$order )
        {
            return $this->respondNotFound('Order does not exist.');
        }

        $order->delete();
        return $this->respondUpdated("Order successfully deleted.");
    }

}
