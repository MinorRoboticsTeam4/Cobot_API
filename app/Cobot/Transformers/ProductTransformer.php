<?php namespace App\Cobot\Transformers;

class ProductTransformer extends Transformer {

    /**
     * Transform a product
     * @param $item
     * @return array
     */
    public function transform($item)
    {
        return [
            'id' => $item['id'],
            'name' => $item['name'],
            'type' => $item['type'],
            'image' => $item['image_path'],
            'strength' => $item['option_strength'],
            'milk' => $item['option_milk'],
            'sugar' => $item['option_sugar'],
            'mug' => (boolean) $item['option_mug'],
        ];
    }
}