<?php namespace App\Http\Controllers;

use App\Cobot\Transformers\ProductTransformer;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use App\Cobot\Transformers\UserTransformer;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Class UsersProductsController
 * @package App\Http\Controllers
 */
class UsersProductsController extends ApiController
{

    /**
     * @var UserTransformer
     */
    protected $userTransformer;
    /**
     * @var ProductTransformer
     */
    protected $productTransformer;

    /**
     * UsersProductsController constructor.
     * @param UserTransformer $userTransformer
     * @param ProductTransformer $productTransformer
     */
    public function __construct(UserTransformer $userTransformer, ProductTransformer $productTransformer)
    {
        $this->userTransformer = $userTransformer;
        $this->productTransformer = $productTransformer;

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

        return $this->respond($this->productTransformer->transformCollection($user->products->all()));
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
        if( Auth::user()->id != $user_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $request['user_id'] = $user_id;
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'name' => 'required|string',
            'type' => 'required|integer',
            'option_strength' => 'required|integer',
            'option_milk' => 'required|integer',
            'option_sugar' => 'required|integer',
            'option_mug'  => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors(), "Parameters failed validation for a product");
        }

        Product::create($request->all());
        return $this->respondCreated("Product successfully created.");
    }

    /**
     * Display the specified resource.
     *
     * @param $user_id
     * @param $product_id
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show($user_id, $product_id)
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

        $product = $user->products->find($product_id);
        if ( !$product )
        {
            return $this->respondNotFound('Product does not exist.');
        }

        return $this->respond([
            'data' => $this->productTransformer->transform($product)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $user_id
     * @param $product_id
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(Request $request, $user_id, $product_id)
    {
        if( Auth::user()->id != $user_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $request['user_id'] = $user_id;
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'name' => 'string',
            'type' => 'integer',
            'option_strength' => 'integer',
            'option_milk' => 'integer',
            'option_sugar' => 'integer',
            'option_mug'  => 'boolean'
        ]);

        if ($validator->fails())
        {
            return $this->respondValidationError($validator->errors(), "Parameters failed validation for a product");
        }

        $user = User::find($user_id);
        if ( !$user )
        {
            return $this->respondNotFound('User does not exist.');
        }

        $product = $user->products->find($product_id);
        if ( !$product )
        {
            return $this->respondNotFound('Product does not exist.');
        }

        $product->update($request->all());
        return $this->respondUpdated("Product successfully updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $user_id
     * @param $product_id
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($user_id, $product_id)
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

        $product = $user->products->find($product_id);
        if ( !$product )
        {
            return $this->respondNotFound('Product does not exist.');
        }

        $product->delete();
        return $this->respondUpdated("Product successfully deleted.");

    }

}
