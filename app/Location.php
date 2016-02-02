<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'location';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'coordinate_x', 'coordinate_y', 'coordinate_z', 'orientation_x', 'orientation_y', 'orientation_z'];



}
