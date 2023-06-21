<?php

namespace App\Models;

use App\Jobs\ProcessCouponExpire;
use App\Jobs\ProcessManualNotification;
use Carbon\CarbonInterface;


/**
 * @property mixed $data
 * @property mixed $id
 * @property mixed $type
 * @property mixed $body
 * @property mixed $title
 * @property ?CarbonInterface $schedule_at
 * @property bool $all_delivery_boys
 * @property bool $all_sellers
 * @property bool $all_customers
 * @property object $actions
 * @property string $image
 */
class ManualNotification extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];

    protected string $imageBaseLocation = 'notification_images/';


    protected $casts = [
        'data' => 'array',
        'schedule_at' => 'datetime',
        'all_customers' => 'boolean',
        'all_sellers' => 'boolean',
        'all_delivery_boys' => 'boolean',
    ];


    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'title' => ['required'],
            'body' => ['required'],
            'all_customers' => ['boolean'],
            'all_sellers' => ['boolean'],
            'all_delivery_boys' => ['boolean'],
            'schedule_at' => ['nullable', 'date',],
            'actions' => ['nullable', 'json']
        ];
    }

    //===================== Functionalities  ====================================//

    public function send_notification()
    {
        $job = ProcessManualNotification::dispatch($this->id)->delay($this->schedule_at ?? now());
//        $job = ProcessManualNotification::dispatch($this->id)->delay($this->schedule_at->addDay() ?? now());
//        dd($job);


    }


    public function getAllTokens(): array
    {
        $ar = [];
        if ($this->all_customers) {
            array_push($ar, ...TokenLog::getAllToken(Customer::class));
        }
        if ($this->all_sellers) {
            array_push($ar, ...TokenLog::getAllToken(Seller::class));
        }
        if ($this->all_delivery_boys) {
            array_push($ar, ...TokenLog::getAllToken(DeliveryBoy::class));
        }
        return $ar;
    }

    // ------------------------- Life Cycle ---------------------------------------------------//

    protected static function boot()
    {
        parent::boot();

        self::updating(function (ManualNotification $model) {
            if($model->isDirty('schedule_at')){
                $nModel = $model->replicateQuietly();
                $model->delete();
                $nModel->save();
                $nModel->send_notification();
            }
        });
    }

}


