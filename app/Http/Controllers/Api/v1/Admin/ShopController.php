<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Helpers\CValidator;
use App\Http\Controllers\Controller;
use App\Http\Resources\AddonResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ModuleResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\Strict\StrictOrderResource;
use App\Http\Resources\Strict\StrictSellerRoleResource;
use App\Http\Resources\Strict\StrictShopPlanHistoryResource;
use App\Http\Resources\Strict\StrictShopRevenueResource;
use App\Http\Resources\Strict\StrictShopReviewResource;
use App\Models\Addon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerRole;
use App\Models\Shop;
use App\Models\ShopPlanHistory;
use App\Models\ShopRevenue;
use App\Models\ShopReview;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class ShopController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $module_id = $request->get('module_id');
        $shops = Shop::withAll();
        if ($module_id) {
            $shops = $shops->where('module_id', $module_id);
        }

        return ShopResource::collection($shops->get());
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $owner_data = $request->get('seller');
        $shop_data = $request->get('shop');
        $owner_validated_data = null;

        if ($owner_data) {
            $owner_validated_data = CValidator::validate($owner_data, Seller::rules(), errorPrefix: 'seller.');
        }

        $shop_validated_data = CValidator::validate($shop_data, Shop::rules(), errorPrefix: 'shop.');


        $shop = new Shop($shop_validated_data);


        DB::transaction(function () use ($request, $shop, $owner_validated_data) {
            $shop->saveLogo($request);
            $shop->saveCoverImage($request);
            $shop->save();
            if ($owner_validated_data) {
                $seller = new Seller($owner_validated_data);
                $seller['password'] = Hash::make($owner_validated_data['password']);
                $seller->saveAvatarImage($request);
                $seller->save();
                $shop->attachOwner($seller->id);
            }
        });

        return $this->createdResponse('Shop is created');
    }

    public function show(Request $request, $id): ShopResource
    {
        return new ShopResource(Shop::withAll()->with('owner')->findOrFail($id));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_SHOP_ID', 0) >= $id && !$request->get('active', true)) {
            return CResponse::demoError('You can\'t deactive shop for demo. Please create one and try on them');
        }
        $shop = Shop::find($id);
        $validated_data = CValidator::validate([...$shop->toArray(), ...$request->all()], Shop::rules($id));
        $shop->update($validated_data);
        if (isset($request['owner_id'])) {
            $shop->attachOwner($validated_data['owner_id']);
        }
        $shop->saveLogo($request);
        $shop->saveCoverImage($request);
        $shop->save();
        return $this->successResponse('Shop is updated');
    }


    public function destroy(Request $request, $id): Response|Application|ResponseFactory
    {
        $shop = Shop::findOrFail($id);
        $shop->archived = true;
        $shop->active = false;
        $shop->approved = false;
        $shop->save();
        return $this->successResponse('Shop is archived');
    }

    public function approve(Request $request, $id): Response|Application|ResponseFactory
    {
        $shop = Shop::findOrFail($id);
        if ($shop->approved) {
            return $this->errorResponse("This shop is already approved");
        }
        $shop->approved = true;
        $shop->archived = false;
        $shop->save();
        return $this->successResponse('Shop is edited');
    }

    public function removeLogo(Request $request, $id): Response|Application|ResponseFactory
    {
        $shop = Shop::findOrFail($id);
        $shop->removeLogo();
        $shop->save();
        return $this->successResponse('Logo is deleted');
    }


    public function removeCoverImage(Request $request, $id): Response|Application|ResponseFactory
    {
        $shop = Shop::findOrFail($id);
        $shop->removeCoverImage();
        $shop->save();
        return $this->successResponse('Cover image is deleted');
    }

    public function orders(Request $request, $id): AnonymousResourceCollection
    {
        return StrictOrderResource::collection(Order::withAll()->where('shop_id', $id)->get());
    }


    public function addons(Request $request, $id): AnonymousResourceCollection
    {
        return AddonResource::collection(Addon::where('shop_id', $id)->get());
    }

    public function products(Request $request, $id): AnonymousResourceCollection
    {
        return ProductResource::collection(Product::withAll()->where('shop_id', $id)->get());
    }

    public function categories(Request $request, $id): AnonymousResourceCollection
    {
        $shop = Shop::with(['module', 'module.categories.subCategories'])->findOrFail($id);
        return CategoryResource::collection($shop->module->categories);
    }

    public function module(Request $request, $id): ModuleResource
    {
        $shop = Shop::with(['module'])->findOrFail($id);
        return new ModuleResource($shop->module);
    }

    public function roles(Request $request, $id): AnonymousResourceCollection
    {
        return StrictSellerRoleResource::collection(SellerRole::where('shop_id', $id)->get());
    }

    public function revenues(Request $request, $id): AnonymousResourceCollection
    {
        return StrictShopRevenueResource::collection(ShopRevenue::with(['shop'])->where('shop_id', $id)->get());
    }

    public function reviews(Request $request, $id): AnonymousResourceCollection
    {
        return StrictShopReviewResource::collection(
            ShopReview::with(['shop', 'customer'])->where('shop_id', $id)->get()
        );
    }

    public function planHistories(Request $request, $id): AnonymousResourceCollection
    {
        $plan_histories = ShopPlanHistory::with(['shop', 'shopPlan'])->where('shop_id', $id)->latest()->get();
        return StrictShopPlanHistoryResource::collection($plan_histories);
    }


    /**
     * @throws ValidationException
     */
    public function upgradePlan(Request $request, $id): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, [
            'shop_plan_id' => ['required'],
        ]);
        $shop = Shop::findOrFail($id);
        $shop->upgradePlan($validated_data['shop_plan_id']);
        return $this->successResponse();
    }
}

