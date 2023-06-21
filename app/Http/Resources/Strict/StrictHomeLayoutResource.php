<?php

namespace App\Http\Resources\Strict;

use App\Models\HomeLayout;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @property mixed $items
 * @property mixed $priority
 * @property mixed $type
 * @property mixed $id
// * @property mixed $images
 * @property mixed $active
 */
class StrictHomeLayoutResource extends JsonResource
{

    public function toArray($request): array
    {
        if ($this->items != null) {
            if (HomeLayout::isProductType($this->type)) {
                $this->items = StrictProductResource::collection($this->items);
            } elseif (HomeLayout::isShopType($this->type)) {
                $this->items = StrictShopResource::collection($this->items);
            }elseif (HomeLayout::isBannerType($this->type)) {
                $this->items = StrictHomeBannerResource::collection($this->items);
            }
        }
//        $storage_images = [];
//        if ($this->images != null) {
//            foreach (json_decode($this->images) as $image) {
//                $storage_images[] = ResourceUtil::getImagePath($image);
//            }
//        }

        $data = [
            'id' => $this->id,
            'type' => $this->type,
            'priority' => $this->priority,
            'active' => $this->active,
        ];

        if ($this->items != null) {
            $data = [...$data, 'items' => $this->items];
        }

        return $data;


    }


}
