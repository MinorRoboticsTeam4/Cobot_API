<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'orders';

    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'location_id', 'delivered_at', 'delivery_status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function products() {
        return $this->hasMany('App\OrderProducts');
    }

    public function location() {
        return $this->belongsTo('App\Location');
    }

}