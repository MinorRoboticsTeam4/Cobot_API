<?php

namespace App\Http\Controllers;

use App\Cobot\Transformers\LocationTransformer;
use App\Location;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocationsController extends ApiController
{

    protected $robot_id = 2;
    protected $locationTransformer;

    /**
     * UsersController constructor.
     * @param LocationTransformer $locationTransformer
     */
    public function __construct(LocationTransformer $locationTransformer)
    {
        $this->locationTransformer = $locationTransformer;

        $this->middleware('auth.basic.once', ['only' => ['update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locations = Location::all();
        if ( count($locations) == 0 )
        {
            return $this->respondNotFound('Locations do not exist.');
        }
        return $this->respond($this->locationTransformer->transformCollection($locations->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param $location_id
     * @return \Illuminate\Http\Response
     */
    public function show($location_id)
    {
        $location = Location::find($location_id);
        if ( !$location )
        {
            return $this->respondNotFound('Location does not exist.');
        }

        return $this->respond($this->locationTransformer->transform($location));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $location_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $location_id)
    {
        if( Auth::user()->id != $this->robot_id )
        {
            return $this->respondInsufficientPermissions('User not authorized for this request.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'coordinates' => 'string',
            'orientation' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors(), "Parameters failed validation for an order");
        }

        $location = Location::find($location_id);
        if ( !$location )
        {
            return $this->respondNotFound('Location does not exist.');
        }

        $location->update($request->all());
        return $this->respondUpdated("Location successfully updated.");
    }
}
