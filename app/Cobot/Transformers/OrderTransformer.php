<?php namespace App\Cobot\Transformers;

use App\Location;
use App\Order;
use DateTime;
use Exception;

class OrderTransformer extends Transformer {

    /**
     * Transform an order
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
            'user' => $item['user_id'],
            'location_id' => $item['location_id'],
            'location' => $location,
            'delivery_status' => $item['delivery_status'],
            'delivered_at' => $item['delivered_at']
        ];
    }

    public function transformForRobot($item)
    {
        $timestamp = strtotime($item['delivered_at']);

        $productTransformer = new ProductTransformer();
        $products = $productTransformer->transformCollection($item->products->all());
        $locationTransformer = new LocationTransformer();
        $location = $locationTransformer->transform($item->location);

        return [
            'order_id' => $item['id'],
            'user_id' => $item['user_id'],
            'delivery_status' => $item['delivery_status'],
            'delivered_at' => $timestamp,
            'location' => $location,
            'products' => $products
        ];
    }
}