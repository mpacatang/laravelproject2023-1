<?php

namespace App\Models;

use App\Jobs\ProcessNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property mixed $data
 * @property mixed $notifiable_id
 * @property mixed $notifiable_type
 * @property mixed $body
 * @property mixed $title
 */
class Notification extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];

    protected $casts = [
        'data' => 'array'
    ];


    public static string $order_type = 'order';
    public static string $other_type = 'other';


    //===================== Functionalities  ====================================//

    public function send_notification(){
        ProcessNotification::dispatch($this);
    }


}
