<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictAdminRevenueResource;
use App\Models\AdminRevenue;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class RevenueController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return StrictAdminRevenueResource::collection(AdminRevenue::all());
    }

}

