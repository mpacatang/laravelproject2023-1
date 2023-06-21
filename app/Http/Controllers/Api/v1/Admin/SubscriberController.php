<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictSubscriberResource;
use App\Models\Subscriber;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class SubscriberController extends Controller
{

    public function index(): AnonymousResourceCollection
    {
        return StrictSubscriberResource::collection(Subscriber::all());
    }
}

