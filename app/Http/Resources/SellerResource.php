<?php

namespace App\Http\Resources;

use App\Http\Resources\Strict\StrictSellerResource;
use Illuminate\Http\Request;

/**
 *
 */
class SellerResource extends StrictSellerResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request => Product
     * @return array
     */


    public function toArray($request): array
    {
        return [
            ...parent::toArray($request)
        ];
    }
}
