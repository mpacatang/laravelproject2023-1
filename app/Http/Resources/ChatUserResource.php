<?php

namespace App\Http\Resources;

use App\Helpers\Util\ImageUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $email
 * @property mixed $last_name
 * @property mixed $first_name
 * @property mixed $id
 * @property mixed $avatar
 * @property mixed $mobile_number
 */
class ChatUserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'avatar' => ResourceUtil::getImagePath($this->avatar),
        ];
    }


}
