<?php namespace App\Cobot\Transformers;

class OrderTransformer extends Transformer {

    /**
     * Transform an order
     * @param $item
     * @return array
     */
    public function transform($item)
    {
        return [
            'id' => $item['id'],
            'user' => $item['user_id'],
            'location' => $item['location'],
            'delivery_status' => $item['delivery_status'],
            'delivered_at' => $item['delivered_at']
        ];
    }
}