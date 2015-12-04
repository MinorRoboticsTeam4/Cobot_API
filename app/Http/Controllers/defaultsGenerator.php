<?php
/**
 * Created by PhpStorm.
 * User: Emil
 * Date: 2015-11-30
 * Time: 14:32
 */

namespace App\Http\Controllers;


use App\Product;

trait DefaultsGenerator
{
    protected $defaultCoffees = [
        [
            'name' => 'Black',
            'type' => 0,
        ],
        [
            'name' => 'Cappuccino',
            'type' => 1,
        ],
        [
            'name' => 'Espresso',
            'type' => 2,
        ],
        [
            'name' => 'Café au Lait',
            'type' => 3,
        ],
        [
            'name' => 'Wiener Melange',
            'type' => 4,
        ],
        [
            'name' => 'Double Espresso',
            'type' => 5,
        ],
        [
            'name' => 'Café Mocca',
            'type' => 6,
        ],
        [
            'name' => 'Café Macchiato',
            'type' => 7,
        ],
        [
            'name' => 'Espresschoc',
            'type' => 8,
        ],
        [
            'name' => 'Hot Chocolate',
            'type' => 9,
        ],
    ];

    public function coffeeGenerator($user_id) {

        $request = [
            'user_id' => $user_id,
            'name' => "Coffee",
            'type' => 0,
            'option_strength' => 2,
            'option_milk' => 2,
            'option_sugar' => 2,
            'option_mug'  => 0
        ];

        foreach($this->defaultCoffees as $coffee) {
            $request['name'] = $coffee['name'];
            $request['type'] = $coffee['type'];
            Product::create($request);
        }

    }

}