<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictProductResource;
use App\Http\Resources\Strict\StrictShopPlanResource;
use App\Http\Resources\Strict\StrictShopReviewResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopRevenue;
use App\Models\ShopReview;
use DateInterval;
use DatePeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class DashboardController extends Controller
{
    public function index(Request $request): array
    {
        $data = [];
        $shop_id = $this->getShopId();

        $shop = Shop::with('shopPlan')->find($shop_id);

        $data['total_revenue'] = ShopRevenue::where('shop_id', $shop_id)->sum('revenue');
        $data['order_count'] = Order::where('shop_id', $shop_id)->count();
        $data['product_count'] = Product::where('shop_id', $shop_id)->count();
        $data['shop_plan'] = new StrictShopPlanResource($shop->shopPlan);


        $data['revenues'] = $this->getRevenues($request);
        $data['product_rated_data'] = StrictProductResource::collection($this->getRatedProduct($request));

        $data['product_selling_data'] = StrictProductResource::collection($this->getSellingProduct($request));
        $data['review_data'] = StrictShopReviewResource::collection($this->getReview($request));

        return $data;
    }

    public function getRevenues($request): array
    {
        $interval = $request->query('revenue_interval', 'monthly');

        $revenues = [];

        if ($interval == 'monthly') {
            $start = now()->modify("-11 month");
            $interval = new DateInterval('P1M');
            $end = now()->modify("+1 month");
            $period = new DatePeriod($start, $interval, $end);
            foreach ($period as $index => $dt) {
                $revenues[$index] = [
                    'date' => $dt->format('Y-m-d H:i:s'),
                    'revenue' => ShopRevenue::where('shop_id', $request->shop_id)->whereMonth(
                        'created_at',
                        $dt->format('m')
                    )
                        ->whereYear('created_at', $dt->format('Y'))
                        ->sum('revenue')
                ];
            }
        } else {
            $start = now()->modify("-30 days");
            $interval = new DateInterval('P1D');
            $end = now()->modify("+1 day");
            $period = new DatePeriod($start, $interval, $end);
            foreach ($period as $index => $dt) {
                $revenues[$index] = [
                    'date' => $dt->format('Y-m-d H:i:s'),
                    'revenue' => ShopRevenue::where('shop_id', $request->shop_id)->whereDate('created_at', $dt)
                        ->sum('revenue')
                ];
            }
        }
        return $revenues;
    }


    public function getRatedProduct(Request $request): Collection|array
    {
        $rating_order = $request->query('rating_order', 'high');

        return Product::with('productImages')->where('shop_id', $this->getShopId())
            ->orderBy('rating', $rating_order == 'high' ? 'desc' : 'asc')->take(5)->get();
    }


    public function getSellingProduct(Request $request): Collection|array
    {
        $selling_order = $request->query('selling_order', 'high');

        return Product::with('productImages')->where('shop_id', $this->getShopId())
            ->orderBy('selling_count', $selling_order == 'high' ? 'desc' : 'asc')->take(5)->get();
    }

    public function getReview(Request $request): Collection|array
    {
        $review_order = $request->query('review_order', 'high');

        return ShopReview::with('customer')->where('shop_id', $this->getShopId())
            ->orderBy('updated_at', $review_order == 'high' ? 'desc' : 'asc')->take(5)->get();
    }


    public function revenues(Request $request): array
    {
        return $this->getRevenues($request);
    }

    public function ratedProducts(Request $request): AnonymousResourceCollection
    {
        return StrictProductResource::collection($this->getRatedProduct($request));
    }

    public function sellingProduct(Request $request): AnonymousResourceCollection
    {
        return StrictProductResource::collection($this->getSellingProduct($request));
    }


}

