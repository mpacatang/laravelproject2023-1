<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Mail\CustomerEmailVerificationMail;
use App\Models\CustomerWallet;
use App\Models\CustomerWalletTransaction;
use http\Env\Response;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use WpOrg\Requests\Exception;

class PaymentCaptureController extends Controller
{

    public function test(Request $request)
    {
        error_log("Called");
        try {


        }catch (\Exception $e){
            error_log($e->getMessage());
        }
//        try {
//            error_log("1");
////            error_log();

//            error_log("2");
//
//

//            return dd;
//            error_log("6");
//        }catch(\Exception $e){
//            error_log($e->getMessage());
//        }


//        info((string)$request->all());
//        $entity = $request->get('payload')['payment']['entity'];
//        $id = $entity['id'];
//        $amount = $entity['amount'] / 100;
//        $customer_id = $entity['notes']['customer_id'];
//        CustomerWallet::addMoney($customer_id, $amount, CustomerWalletTransaction::$razorpay_method, $id);
//        print_r($request->all());
//        error_log(implode(' ', $request->all()));
    }

    public function razorpay(Request $request)
    {
        if ($request->get('event')== "payment.captured") {
            $payload = $request->get('payload');
            $entity = $payload['payment']['entity'];
            $id = $entity['id'];
            $amount = $entity['amount'] / 100;
            $customer_id = $entity['notes']['customer_id'];
            CustomerWallet::addMoney($customer_id, $amount, CustomerWalletTransaction::$razorpay_method, $id);
        }
    }

    public function stripe(Request $request)
    {

        $object = $request->get('data')['object'];
        $id = $object['id'];
        $amount = $object['amount'] / 100;
        $customer_id = $object['metadata']['customer_id'];
        CustomerWallet::addMoney($customer_id, $amount, CustomerWalletTransaction::$stripe_method, $id);
    }

    public function paypal(Request $request)
    {

        $object = $request->get('resource')['purchase_units'][0];
        $id = $request['id'];
        $amount = $object['amount']['value'];
        $customer_id = $object['reference_id'];
        CustomerWallet::addMoney($customer_id, $amount, CustomerWalletTransaction::$paypal_method, $id);
    }

    public function flutterwave(Request $request)
    {

        $txRef = $request->get('txRef');
        $customer_id = substr(strrchr($txRef, '____'), 1);
        $id = $request->get('id');
        $amount = $request->get('amount');
        CustomerWallet::addMoney($customer_id, $amount, CustomerWalletTransaction::$flutterwave_method, $id);
    }

    public function paystack(Request $request)
    {

        $entity = $request->get('data');
        $id = $entity['id'];
        $amount = $entity['amount'] / 100;
        $customer_id = $entity['metadata']['customer_id'];
        CustomerWallet::addMoney($customer_id, $amount, CustomerWalletTransaction::$paystack_method, $id);

    }

    public function cashfree(Request $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $entity = $request->json()->get('data');
        $id = $entity['link_id'];

        $amount = $entity['link_amount_paid'];

        try {
            $customer_id = $entity['link_notes']['customer_id'];
            if (isset($customer_id)) {
                CustomerWallet::addMoney($customer_id, $amount, CustomerWalletTransaction::$cashfree_method, $id);
            }
        } catch (\Exception) {

        }
        return $this->successResponse();

    }

}
