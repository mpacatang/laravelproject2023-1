<?php

namespace App\Http\Controllers;

use App\Helpers\Util\ImageUtil;
use App\Models\ChatParticipant;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kreait\Firebase\Exception\AuthException;
use MercadoPago\Payment;
use MercadoPago\SDK;

//use App\Models\Conversation;


class TestController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * @param Request $request
     * @throws AuthException
     * @throws Exception
     */
    public function test(Request $request)
    {
//        dd(Conversation::createOrFind(Customer::class, 1, Seller::class, 4));

        $participant_tokens = ChatParticipant::with('user', 'user.activeTokenLogs')->where('chat_id', 1)
            ->where('id', '!=', 1)->get()->pluck('user.activeTokenLogs.*.fcm_token')->flatten()->toArray();

        dd($participant_tokens);
        return;
        SDK::setAccessToken("TEST-4938242176749846-071016-302e46119db48ffd19fc6564e2789db0-1158141068");
        $payment = new Payment();

        $payment->transaction_amount = 141;
        $payment->token = "YOUR_CARD_TOKEN";
        $payment->description = "Ergonomic Silk Shirt";
        $payment->installments = 1;
//        $payment->payment_method_id = "visa";
        $payment->payer = array(
            "email" => "larue.nienow@email.com"
        );

        $payment->save();
        dd($payment);
        return;
        return;
//        $url = "https://coderthemes.com/assets/images/work/ubold-admin.png";
        $image = ImageUtil::fileGetContentsCurl("https://shopy.getappui.com/storage/product_images/shop_1c.png");
        dd($image);
        $temp = tempnam(sys_get_temp_dir(), 'TMP_');
        echo $temp; // output => C:\Windows\Temp\TMP2142.tmp

        file_put_contents($temp, file_get_contents($url));

        $content = file_get_contents($temp);

        $size = getimagesize($temp);
        dd($size);
        $job = app('queue')->getDatabase()->table('jobs')->whereJsonContains('payload', []);
        dd($job);


//        Job::
//        return "treuss";
//        return;
//        return BusinessSetting::all();
//        return Cache::get('business_settings');
//        $factory = (new Factory)->withServiceAccount('../firebase_credentials.json');
//        return $factory->createAuth()->getUser('eMJ8H28byQUAIm0e8yMr39EPB32');


    }
}
/*
public function index()
{

}

public function create()
{

}


public function store(Request $request)
{

}

public function show($id)
{
}


public function edit($id)
{

}


public function update(Request $request)
{

}


public function destroy($id){

}
*/
