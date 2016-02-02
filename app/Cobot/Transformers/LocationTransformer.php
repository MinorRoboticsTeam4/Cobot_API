<?php namespace App\Cobot\Transformers;

use App\Location;
use Exception;

class LocationTransformer extends Transformer {

    /**
     * Transform a location
     * @param $item
     * @return array
     */
    public function transform($item)
    {
        return [
            'id' => $item['id'],
            'name' => $item['name'],
            'coordinate_x' => $item['coordinate_x'],
            'coordinate_y' => $item['coordinate_y'],
            'coordinate_z' => $item['coordinate_z'],
            'orientation_x' => $item['orientation_x'],
            'orientation_y' => $item['orientation_y'],
            'orientation_z' => $item['orientation_z']
        ];
    }
}