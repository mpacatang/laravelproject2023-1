<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ProductImageController extends Controller
{
    public function destroy(Request $request, $id): Response|Application|ResponseFactory
    {
        $productImage = ProductImage::findOrFail($id);
        $productImage->removeImage();
        $productImage->delete();
        return $this->successResponse('Product image is deleted');
    }
}

