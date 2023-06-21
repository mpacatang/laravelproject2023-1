<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ProductReviewController extends Controller
{

    public function destroy(Request $request, $id): Application|ResponseFactory|Response
    {
        ProductReview::findOrFail($id)->delete();
        return $this->noContentResponse();
    }

}

