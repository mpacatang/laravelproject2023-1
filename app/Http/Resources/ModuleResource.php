<?php

namespace App\Http\Resources;

use App\Http\Resources\Strict\StrictModuleResource;
use Illuminate\Http\Request;

/**
 *
 */
class ModuleResource extends StrictModuleResource
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
