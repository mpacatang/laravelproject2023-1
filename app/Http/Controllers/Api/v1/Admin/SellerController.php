<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\SellerResource;
use App\Http\Resources\Strict\StrictSellerResource;
use App\Models\Seller;
use App\Models\Shop;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class SellerController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $module_id = $request->get('module_id');
        $sellers = Seller::with(['role', 'shop']);
        if($module_id){
            $sellers = $sellers->whereHas('shop', function ($q) use ($module_id,) {
                $q->where('module_id', $module_id);
            });
        }

        return SellerResource::collection($sellers->get());
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {

        $validated_data = $this->validate($request, Seller::rules());
        $shop = Shop::findOrFail($validated_data['shop_id']);
        $validated_data['password'] = Hash::make($validated_data['password']);
        $seller = new Seller($validated_data);
        $seller->saveAvatarImage($request);
        DB::transaction(function () use ($shop, $seller) {
            $seller->save();
            if($seller->is_owner){
                $shop->attachOwner($seller->id);
            }
        });

        return $this->createdResponse('Seller is created');
    }

    public function show(Request $request, $id): StrictSellerResource
    {
        return new StrictSellerResource(Seller::withAll()->findOrFail($id));
    }


    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_SELLER_ID',0)>=$id) {
            return CResponse::demoCreateNewError('seller');
        }

        $seller = Seller::findOrFail($id);
        $validated_data = $this->validate($request, Seller::rules($id));
        $seller->fill($validated_data);
        if ($request['password'] != null) {
            $seller->password = Hash::make($validated_data['password']);
        }
        $seller->saveAvatarImage($request);
        $seller->save();
        return $this->successResponse('Seller updated');
    }


    public function removeAvatar(Request $request, $id): Response|Application|ResponseFactory
    {
        $seller = Seller::findOrFail($id);
        $seller->removeAvatar();
        $seller->save();
        return $this->successResponse('Avatar removed');
    }


    public function availableOwners(Request $request): AnonymousResourceCollection
    {
        return StrictSellerResource::collection(Seller::withAll()
            ->where('is_owner', true)
            ->where('shop_id', '=', null)->get());
    }


}

