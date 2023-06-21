<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Models\OrderMedia;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class OrderMediaController extends Controller
{


    public function destroy(Request $request, $id): Response|Application|ResponseFactory
    {
        $order_media = OrderMedia::whereHas('order', function ($query) {
            $query->where('customer_id', $this->getUserId());
        })->findOrFail($id);

        $order_media->removeMedia();
        return Response(null, ResponseAlias::HTTP_NO_CONTENT);
    }


}

