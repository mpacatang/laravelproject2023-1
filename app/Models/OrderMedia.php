<?php

namespace App\Models;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property mixed $order_id
 * @property mixed|string $media
 */
class OrderMedia extends BaseModel
{

    protected $table = 'order_medias';


    //===================== Functionalities  ====================================//


    public static function saveOrderMedia(Order $order, $media): bool
    {
        try {
            $url = "order_images/" . Str::random(16);
            $data = base64_decode($media);

            if (Storage::disk('public')->put($url, $data)) {
                $order_media = new OrderMedia();
                $order_media->order_id = $order->id;
                $order_media->media = $url;
                return $order_media->save();
            }
        } catch (Exception) {

        }

        return false;
    }

    public function removeMedia(): ?bool
    {
        if (!$this->media || Storage::disk('public')->delete($this->media)) {
            return $this->delete();
        }
        return false;
    }

    //===================== RelationShips  ====================================//


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

}
