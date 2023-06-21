<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @property mixed $name
 * @property mixed $id
 * @property mixed $category_id
 * @property mixed $active
 */
class SubCategoryResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name'=> $this->name,
            'category_id' => $this->category_id,
            'active'=> $this->active,
            'category'=> new CategoryResource($this->whenLoaded('category'))
        ];
    }
}
