<?php namespace App\Cobot\Transformers;

use App\Location;
use Exception;

class UserTransformer extends Transformer {

    /**
     * Transform a user
     * @param $item
     * @return array
     */
    public function transform($item)
    {
        try {
            $location = Location::find($item['location_id'])->name;
        } catch(Exception $e) {
            $location = "Location not available";
        }
        return [
            'id' => $item['id'],
            'name' => $item['name'],
            'location_id' => $item['location_id'],
            'product_id' => $item['product_id'],
            'location' => $location
        ];
    }
}