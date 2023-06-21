<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\Strict\StrictShopPlanResource;
use App\Models\AdminRevenue;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopPlan;
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

        $module_id = $request->get('module_id');

        $orders = Order::query();
        $shops = Shop::query();
        $revenues = AdminRevenue::query();
        if($module_id){
            $orders=$orders->where('module_id', $module_id);
            $shops=$shops->where('module_id', $module_id);
            $revenues=$revenues->where('module_id', $module_id);
        }

        $data['order_count'] =$orders->count();


        $data['shop_count'] = $shops->count();
        $data['customer_count'] = Customer::count();
        $data['total_revenue'] = $revenues->sum('revenue');


        $interval = $request->query('revenue_interval', 'monthly');
        $category_id = $request->query('category_id');




        $data['revenues'] = $this->getRevenues($interval, $module_id);
        $data['shop_plan_data'] = $this->getShopPlanData($module_id);
        $data['category_product_popular_data'] = ProductResource::collection($this->getCategoryPopularProduct($category_id, $module_id));
        $data['most_rated_product_data'] = ProductResource::collection($this->getMostRatedProduct($module_id));

        return $data;
    }

    public function getRevenues($interval = 'monthly', $module_id=null): array
    {
        $revenues = [];



        if ($interval == 'monthly') {
            $start = now()->modify("-11 month");
            $interval = new DateInterval('P1M');
            $end = now()->modify("+1 month");
            $period = new DatePeriod($start, $interval, $end);
            foreach ($period as $index => $dt) {
                $admin_revenues = AdminRevenue::query();
                if($module_id){
                    $admin_revenues =$admin_revenues->where('module_id', $module_id);
                }
                $revenues[$index] = [
                    'date' => $dt->format('Y-m-d H:i:s'),
                    'revenue' => $admin_revenues->whereMonth('created_at', $dt->format('m'))
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
                $admin_revenues = AdminRevenue::query();
                if($module_id){
                    $admin_revenues =$admin_revenues->where('module_id', $module_id);
                }
                $revenues[$index] = [
                    'date' => $dt->format('Y-m-d H:i:s'),
                    'revenue' => $admin_revenues->whereDate('created_at', $dt)
                        ->sum('revenue')
                ];
            }
        }
        return $revenues;
    }

    public function getShopPlanData($module_id=null): array
    {
        $shop_plans = ShopPlan::all();
        $data = [];
        foreach ($shop_plans as $index => $shop_plan) {
            $shops = Shop::query();
            if($module_id){
                $shops->where('module_id', $module_id);
            }

            $data[$index] = [
                'shop_plan' => new StrictShopPlanResource($shop_plan),
                'count' => $shops->where('shop_plan_id', $shop_plan->id)->count()
            ];
        }
        return $data;
    }

    public function getCategoryPopularProduct($category_id = null, $module_id=null): Collection|array
    {
        return Product::with('productImages')
            ->when($category_id != null, function ($q) use ($category_id) {
                return $q->where('category_id', $category_id);
            })->when($module_id != null, function ($q) use ($module_id) {
                return $q->where('module_id', $module_id);
            })
            ->orderBy('selling_count', 'desc')->take(5)->get();
    }

    public function getMostRatedProduct($module_id=null): Collection|array
    {
        return Product::with('productImages')
            ->when($module_id != null, function ($q) use ($module_id) {
                return $q->where('module_id', $module_id);
            })
            ->orderBy('rating', 'desc')->take(5)->get();
    }

    public function revenues(Request $request): array
    {
        $interval = $request->query('revenue_interval', 'monthly');
        $module_id = $request->query('module_id');
        return $this->getRevenues($interval,$module_id);
    }


    public function categoryProducts(Request $request): AnonymousResourceCollection
    {

        $category_id = $request->query('category_id');
        $module_id = $request->query('module_id');

        return ProductResource::collection($this->getCategoryPopularProduct($category_id, $module_id));
    }


}

