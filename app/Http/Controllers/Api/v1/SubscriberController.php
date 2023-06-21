<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class SubscriberController extends Controller
{

    /**
     * @throws ValidationException
     */
    public function create(Request $request,): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, Subscriber::rules());
        $subscriber = new Subscriber($validated_data);
        $subscriber->save();
        return $this->successResponse();
    }
}

