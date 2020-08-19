<?php

namespace App\Serializers;

use League\Fractal\Serializer\ArraySerializer;

class ErrorSerializer extends ArraySerializer
{
    /**
     * Serialize a collection.
     *
     * @param  string  $resourceKey
     * @param  array   $data
     * @return array
     */
    public function collection($resourceKey, array $data)
    {
        return ['error' => $data];
    }

    /**
     * Serialize an item.
     *
     * @param  string  $resourceKey
     * @param  array   $data
     * @return array
     */
    public function item($resourceKey, array $data)
    {
        return ['error' => $data];
    }

    /**
     * Serialize null resource.
     *
     * @return array
     */
    public function null()
    {
        return ['error' => []];
    }
}
