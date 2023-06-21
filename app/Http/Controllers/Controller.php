<?php

namespace App\Http\Controllers;

use App\Helpers\CResponse;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\DeliveryBoy;
use App\Models\Seller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as RouteController;


class Controller extends RouteController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public Request $request;

    /**
     * @return int|null
     */
    public function getShopId(): ?int
    {
        return $this->request->get('shop_id');
    }

    public function getUserId(): ?int
    {
        return $this->request->get('user_id');
    }

    public function user(): Admin|Seller|Customer|DeliveryBoy|null
    {
        return $this->request->user();
    }

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function successResponse($message = ''): Response|Application|ResponseFactory
    {
        return CResponse::success($message);
    }

    public function createdResponse($message = ''): Response|Application|ResponseFactory
    {
        return CResponse::created($message);
    }

    public function acceptedResponse($message = ''): Response|Application|ResponseFactory
    {
        return CResponse::accepted($message);
    }

    public function noContentResponse($message = ''): Response|Application|ResponseFactory
    {
        return CResponse::no_content($message);
    }

    public function errorResponse($error = 'Something went wrong!!!'): Response|Application|ResponseFactory
    {
        return CResponse::error($error);
    }

    public function validationErrorResponse($error): Response|Application|ResponseFactory
    {
        return CResponse::validation_error($error);
    }

}
