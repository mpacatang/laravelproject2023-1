<?php

namespace App\Http\Resources;

use App\Http\Resources\Strict\StrictModuleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $description
 * @property mixed $name
 * @property mixed $id
 * @property mixed $image
 * @property mixed $active
 * @property mixed $module_id
 */
class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'active'=> $this->active,
            'module_id'=> $this->module_id,
            'image' => ResourceUtil::getImagePath($this->image),
            'sub_categories' => SubCategoryResource::collection($this->whenLoaded('subCategories')),
            'module' => new  StrictModuleResource($this->whenLoaded('module'))
        ];
    }
}
