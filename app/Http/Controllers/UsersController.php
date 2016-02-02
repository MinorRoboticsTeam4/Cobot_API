<?php namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Cobot\Transformers\UserTransformer;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Class UsersController
 * @package App\Http\Controllers
 */
class UsersController extends ApiController
{

    use DefaultsGenerator;

    /**
     * @var UserTransformer
     */
    protected $userTransformer;

    /**
     * UsersController constructor.
     * @param UserTransformer $userTransformer
     */
    public function __construct(UserTransformer $userTransformer)
    {
        $this->userTransformer = $userTransformer;

        $this->middleware('auth.basic.once', ['only' => ['show', 'update', 'destroy', 'authenticate']] );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|min:1',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'location_id' => 'integer',
            'product_id' => 'integer'
        ]);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors(), "Parameters failed validation for a user");
        }

        $request['password'] = bcrypt($request['password']);
        $user = User::create($request->all());

        $this->coffeeGenerator($user->id);

        return $this->respondCreated($this->userTransformer->transform($user));
    }

    /**
     * Display the specified user.
     *
     * @param $user_id
     * @return \Illuminate\Http\Response
     */
    public function show($user_id)
    {
        $user = Auth::user();
        if ( !$user )
        {
            return $this->respondNotFound('User does not exist.');
        }

        if( $user->id != $user_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        return $this->respond($this->userTransformer->transform($user));
    }

    /**
     * Display the specified user.
     * @return \Illuminate\Http\Response
     */
    public function authenticate()
    {
        $user = Auth::user();
        if ( !$user )
        {
            return $this->respondNotFound('User does not exist.');
        }

        return $this->respond($this->userTransformer->transform($user));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $user_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id)
    {
        $user = Auth::user();
        if( $user->id != $user_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ' min:4',
            'email' => 'unique:users|email',
            'password' => 'min:6',
            'location_id' => 'integer',
            'product_id' => 'integer'
        ]);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors(), "Parameters failed validation for a user");
        }

        $user->update($request->all());
        return $this->respond($this->userTransformer->transform($user));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $user_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_id)
    {
        if( Auth::user()->id != $user_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        User::find($user_id)->delete();
        return $this->respondUpdated("User successfully deleted.");
    }



}
