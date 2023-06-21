<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ResourceUtil
{
    static function getImagePath($image): ?string
    {
        return $image != null ? Storage::url($image) : null;
    }

    static function getFormattedTime($time): ?string{
        if($time==null)
            return null;
        return Carbon::parse($time)->format('H:i');
    }
}
