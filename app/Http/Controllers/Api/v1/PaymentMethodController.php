<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\Request\CCurlRequest;
use App\Helpers\Request\CCurlRequestType;
use App\Helpers\Util\StringUtil;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictCustomerResource;
use App\Models\BusinessSetting;
use App\Models\Customer;
use App\Models\TokenLog;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Razorpay\Api\Api;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Yabacon\Paystack;
use Yabacon\Paystack\Exception\ApiException;


class PaymentMethodController extends Controller
{

    protected string $success_url;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->success_url = route('payments.success');
    }


    public function public_data(Request $request): Application|ResponseFactory|Response|array
    {
        $token = $request->query('token');
        $customer = null;
        if ($token != null) {
            $customer = TokenLog::getUserFromToken(Customer::class, $token);
        }
        if ($customer == null) {
            return $this->errorResponse("User is not found");
        }

        return [
            'currency_code' => BusinessSetting::getCurrencyCode(),
            'customer' => new StrictCustomerResource($customer),
            'business' => [
                'name' => BusinessSetting::getBusinessName()
            ],
            'razorpay' => [
                'enable' => filter_var(
                    BusinessSetting::_get(BusinessSetting::$payment_razorpay_enable),
                    FILTER_VALIDATE_BOOLEAN
                ),
            ],
            'stripe' => [
                'enable' => filter_var(
                    BusinessSetting::_get(BusinessSetting::$payment_stripe_enable),
                    FILTER_VALIDATE_BOOLEAN
                ),
            ],
            'paypal' => [
                'enable' => filter_var(
                    BusinessSetting::_get(BusinessSetting::$payment_paypal_enable),
                    FILTER_VALIDATE_BOOLEAN
                ),
            ],
            'flutterwave' => [
                'enable' => filter_var(
                    BusinessSetting::_get(BusinessSetting::$payment_flutterwave_enable),
                    FILTER_VALIDATE_BOOLEAN
                ),
            ],
            'paystack' => [
                'enable' => filter_var(
                    BusinessSetting::_get(BusinessSetting::$payment_paystack_enable),
                    FILTER_VALIDATE_BOOLEAN
                ),
            ],
            'cashfree' => [
                'enable' => filter_var(
                    BusinessSetting::_get(BusinessSetting::$payment_razorpay_enable),
                    FILTER_VALIDATE_BOOLEAN
                ),
            ],
            'mercado' => [
                'enable' => filter_var(
                    BusinessSetting::_get(BusinessSetting::$payment_mercado_enable),
                    FILTER_VALIDATE_BOOLEAN
                ),
                'public_key' => BusinessSetting::_get(BusinessSetting::$payment_mercado_public_key),
            ],
        ];
    }


    public function createRazorpayCheckout(Request $request): Response|array|Application|ResponseFactory
    {
        try {
            $token = $request->query('token');
            $amount = $request->query('amount');

            if ($amount == null) {
                return $this->errorResponse("Please set amount for creating checkout");
            }
            $customer = null;
            if ($token != null) {
                $customer = TokenLog::getUserFromToken(Customer::class, $token);
            }

            if ($customer == null) {
                return $this->errorResponse("User is not found");
            }

            $key = BusinessSetting::_get(BusinessSetting::$payment_razorpay_id);
            $secret_key = BusinessSetting::_get(BusinessSetting::$payment_razorpay__secret_key);
            $currency_code = BusinessSetting::getCurrencyCode();

            if ($secret_key == null) {
                return $this->errorResponse("Razorpay key is not set by admin");
            }

            $api = new Api($key, $secret_key);
            $link = $api->paymentLink->create(
                array(
                    'amount' => $amount * 100,
                    'currency' => $currency_code,
                    'reference_id' => StringUtil::generateRandomString(),
                    'description' => 'For XYZ purpose',
                    'customer' => array(
                        'name' => $customer->first_name,
                        'email' => $customer->email ?? 'test@mail.com',
                        'contact' => $customer->mobile_number
                    ),
                    'notes' => array(
                        "customer_id" => $customer->id,
                    ),
                    'callback_url' => $this->success_url,
                    'callback_method' => 'get'
                )
            );


            return ['checkout_url' => $link['short_url']];
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function createCashfreeCheckout(Request $request): Response|array|Application|ResponseFactory
    {
        try {
            $token = $request->query('token');
            $amount = $request->query('amount');

            if ($amount == null) {
                return $this->errorResponse("Please set amount for creating checkout");
            }
            $customer = null;
            if ($token != null) {
                $customer = TokenLog::getUserFromToken(Customer::class, $token);
            }

            if ($customer == null) {
                return $this->errorResponse("User is not found");
            }

            $app_id = BusinessSetting::_get(BusinessSetting::$payment_cashfree_app_id);
            $secret_key = BusinessSetting::_get(BusinessSetting::$payment_cashfree__secret_key);
            $currency_code = BusinessSetting::getCurrencyCode();

//            if ($secret_key == null) {
//                return CustomResponse::error("Razorpay key is not set by admin");
//            }

            $url = "https://sandbox.cashfree.com/pg/links";
            $headers = [
                "accept: application/json",
                "content-type: application/json",
                "x-api-version: 2022-09-01",
                "x-client-id: " . $app_id,
                "x-client-secret: " . $secret_key
            ];

            $id = StringUtil::generateRandomString();
            $data = [
                "link_amount" => $amount,
                "link_id" => $id,
                "link_currency" => $currency_code,
                "link_purpose" => "Customer wallet",
                "customer_details" => [

                    "customer_phone" => "9876543210"
                ],
                "link_notes" => [
                    "customer_id" => "$customer->id",
                ],
                "link_meta" => [
                    "return_url" => $this->success_url . "?link_id={link_id}"
                ],
                "link_notify" => [
                    "send_sms" => false
                ]
            ];
            $data = json_encode($data);

            $response = CCurlRequest::request($url, CCurlRequestType::POST, header: $headers, data: $data);

            if ($response->is_success()) {
                return [
                    'checkout_url' => (json_decode($response->data))->link_url
                ];
            }
            return $this->errorResponse($response->error);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function createStripeCheckout(Request $request): Response|array|Application|ResponseFactory
    {
        try {
            $token = $request->query('token');
            $amount = $request->query('amount');

            if ($amount == null) {
                return $this->errorResponse("Please set amount for creating checkout");
            }
            $customer = null;
            if ($token != null) {
                $customer = TokenLog::getUserFromToken(Customer::class, $token);
            }

            if ($customer == null) {
                return $this->errorResponse("User is not found");
            }

            $stripe_key = BusinessSetting::_get(BusinessSetting::$payment_stripe__secret_key);
            $currency_code = BusinessSetting::getCurrencyCode();

            if ($stripe_key == null) {
                return $this->errorResponse("Stripe key is not set by admin");
            }

            Stripe::setApiKey($stripe_key);
            $session = Session::create([
                'payment_method_types' => ['card'],
                'client_reference_id' => 1,
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $currency_code,
                            'unit_amount' => ($amount) * 100,
                            'product_data' => [
                                'name' => BusinessSetting::getBusinessName(),
                            ],
                        ],
                        'quantity' => 1,

                    ]
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'customer_id' => $customer->id,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => $this->success_url,
                'cancel_url' => $this->success_url,
            ]);
            return [
                'checkout_url' => $session->url
            ];
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    private function _getPaypalAccessToken()
    {
        $headers = array();
        $body = ["grant_type" => "client_credentials"];
        $body = http_build_query($body);
        $client_id = BusinessSetting::_get(BusinessSetting::$payment_paypal_client_id);
        $secret_id = BusinessSetting::_get(BusinessSetting::$payment_paypal__secret_key);

        $response = CCurlRequest::request(
            'https://api-m.sandbox.paypal.com/v1/oauth2/token',
            type: CCurlRequestType::POST,
            header: $headers,
            data: $body,
            userpwd: $client_id . ":" . $secret_id
        );
        if ($response->is_success()) {
            return (json_decode($response->data))->access_token;
        } else {
            error_log($response->error);
            return null;
        }
    }

    public function createPaypalCheckout(Request $request): Response|array|Application|ResponseFactory
    {
        try {
            $token = $request->query('token');
            $amount = $request->query('amount');

            if ($amount == null) {
                return $this->errorResponse("Please set amount for creating checkout");
            }
            $customer = null;
            if ($token != null) {
                $customer = TokenLog::getUserFromToken(Customer::class, $token);
            }

            if ($customer == null) {
                return $this->errorResponse("User is not found");
            }

            $currency = BusinessSetting::getCurrencyCode();
            $access_token = $this->_getPaypalAccessToken();

            $headers = array();
            $headers[] = "Content-type: application/json";

            $headers[] = "Authorization: Bearer " . $access_token;


            $data = [
                "intent" => "CAPTURE",
                "metadata" => [
                    'customer_id' => 1
                ],
                "purchase_units" => [
                    [
                        "items" => [
                            [
                                "name" => "Add Money",
                                "description" => "add money to customer wallet",
                                "customer_id" => 1,
                                "quantity" => "1",
                                "unit_amount" => [
                                    "currency_code" => $currency,
                                    "value" => $amount
                                ]
                            ]
                        ],
                        "reference_id" => $customer->id,
                        "amount" => [
                            "currency_code" => $currency,
                            "value" => $amount,
                            "breakdown" => [
                                "item_total" => [
                                    "currency_code" => $currency,
                                    "value" => $amount
                                ]
                            ]
                        ]
                    ]
                ],
                "application_context" => [
                    'return_url' => $this->success_url,
                    "cancel_url" => $this->success_url
                ]
            ];

            $data = json_encode($data);


            $response = CCurlRequest::request(
                'https://api-m.sandbox.paypal.com/v2/checkout/orders',
                type: CCurlRequestType::POST,
                header: $headers,
                data: $data
            );
            if ($response->is_success()) {
                $ob = json_decode($response->data, true);
//                return $ob;
                $links = $ob['links'];

                foreach ($links as $link) {
                    if ($link['rel'] == 'approve') {
                        return ['checkout_url' => $link['href']];
                    }
                }
            }
            return $this->errorResponse($response->error);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    public function createFlutterwaveCheckout(Request $request): Response|array|Application|ResponseFactory
    {
        try {
            $token = $request->query('token');
            $amount = $request->query('amount');

            if ($amount == null) {
                return $this->errorResponse("Please set amount for creating checkout");
            }
            $customer = null;
            if ($token != null) {
                $customer = TokenLog::getUserFromToken(Customer::class, $token);
            }

            if ($customer == null) {
                return $this->errorResponse("User is not found");
            }

            $currency = BusinessSetting::getCurrencyCode();

            $secret_key = BusinessSetting::_get(BusinessSetting::$payment_flutterwave__secret_key);
            error_log($secret_key);
            $headers = array();
            $headers[] = "Content-type: application/json";

            $headers[] = "Authorization: Bearer " . $secret_key;


            $data = [
                "tx_ref" => StringUtil::generateRandomString(14) . "____" . $customer->id,
                "amount" => $amount,
                "currency" => "NGN",
                "redirect_url" => $this->success_url,
                "meta" => [
                    'customer_id' => $customer->id,
                ],
                "customer" => [
                    "id" => $customer->id,
                    'customer_id' => $customer->id,
                    "email" => $customer->email ?? "shopy@gmail.com",
                    "phonenumber" => $customer->mobile_number,
                    "name" => $customer->first_name,
                    "customertoken" => $customer->id,
                    "token" => 2
                ],
//                "customizations" => [
//                    "title" => "Pied Piper Payments",
//                    "logo" > "http://www.piedpiper.com/app/themes/joystick-v27/images/logo.png"
//                ]
            ];

            $data = json_encode($data);

            $response = CCurlRequest::request(
                'https://api.flutterwave.com/v3/payments',
                type: CCurlRequestType::POST,
                header: $headers,
                data: $data
            );
            if ($response->is_success()) {
                $ob = json_decode($response->data, true);
                return ['checkout_url' => $ob['data']['link']];
            }
            return $this->errorResponse($response->error);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function createPaystackCheckout(Request $request): Response|array|Application|ResponseFactory
    {
        try {
            $token = $request->query('token');
            $amount = $request->query('amount');

            if ($amount == null) {
                return $this->errorResponse("Please set amount for creating checkout");
            }
            $customer = null;
            if ($token != null) {
                $customer = TokenLog::getUserFromToken(Customer::class, $token);
            }

            if ($customer == null) {
                return $this->errorResponse("User is not found");
            }


            $secret_key = BusinessSetting::_get(BusinessSetting::$payment_paystack__secret_key);

            $paystack = new Paystack($secret_key);
            try {
                $tranx = $paystack->transaction->initialize([
                    'amount' => $amount * 100,
                    'email' => $customer->email ?? 'example@shopy.com',
                    'reference' => StringUtil::generateRandomString(),
                    'metadata' => [
                        'customer_id' => $customer->id,
                    ]
                ]);
                return ['checkout_url' => $tranx->data->authorization_url];
            } catch (ApiException $e) {
                return $this->errorResponse($e->getMessage());
            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }



    //TODO: ---- In development
//    public function createPaytmCheckout(Request $request)
//    {
//        try {
//            $token = $request->query('token');
//            $amount = $request->query('amount');
//
//            if ($amount == null) {
//                return CustomResponse::error("Please set amount for creating checkout");
//            }
//            $customer = null;
//            if ($token != null) {
//                $customer = TokenLog::getUserFromToken(Customer::class, $token);
//            }
//
//            if ($customer == null) {
//                return CustomResponse::error("User is not found");
//            }
//
//            $currency = BusinessSetting::getCurrencyCode();
//
//            $mid = "dpvjhS47657551410967";
//            $mkey = "3QHRhB#qxr2Yi7h4";
//
//            $headers = array("Content-Type: application/json");
//
//            $paytmParams = array();
//
//            $pBody =  array(
//                "mid"             => "dpvjhS47657551410967",
//                "linkType"        => "GENERIC",
//                "linkDescription" => "Test Payment",
//                "linkName"        => "Test",
//            );
//            $paytmParams["body"] = array(
//                "mid"             => "dpvjhS47657551410967",
//                "linkType"        => "GENERIC",
//                "linkDescription" => "Test Payment",
//                "linkName"        => "Test",
//            );
//
//            /*
//            * Generate checksum by parameters we have in body
//            * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys
//            */
//            $checksum = PaytmChecksum::generateSignature(json_encode($pBody, JSON_UNESCAPED_SLASHES), $mkey);
//
//            $paytmParams["head"] = array(
//                "tokenType"	      => "AES",
//                "signature"	      => $checksum
//            );
//
//            $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
//
//
//
//            $response = CRequest::request('https://securegw-stage.paytm.in/link/create',
//                type: CRequestType::POST, header: $headers, data: $post_data);
//            if ($response->is_success()) {
////                $ob = json_decode($response->data, true);
//////                return $ob;
////                $links = $ob['links'];
////
////                foreach ($links as $link) {
////                    if ($link['rel'] == 'approve') {
////                        return ['checkout_url' => $link['href']];
////                    }
////                }
//                return $response->data;
//            }
//            return $response->error;
//
////            return CustomResponse::error();
//
//        } catch (Exception $e) {
//            return $e;
////            return CustomResponse::error();
//        }
//    }

}
