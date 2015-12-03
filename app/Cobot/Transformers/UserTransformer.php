<?php namespace App\Cobot\Transformers;

class UserTransformer extends Transformer {

    /**
     * Transform a user
     * @param $item
     * @return array
     */
    public function transform($item)
    {
        return [
            'id' => $item['id'],
            'name' => $item['name'],
            'location' => $item['location']
        ];
    }
}