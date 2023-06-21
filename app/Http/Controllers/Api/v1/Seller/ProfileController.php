<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Helpers\CResponse;
use App\Helpers\CValidator;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictSellerResource;
use App\Models\Seller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{

    public function show(Request $request): StrictSellerResource
    {
        $seller = $request->user();
        $seller->load('role');
        return new StrictSellerResource($seller);

    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request): Application|ResponseFactory|Response|StrictSellerResource
    {

        $seller = $request->user();
        if (env('DEMO', false) && env('DEMO_MAX_SELLER_ID', 0) >= $this->getUserId()) {
            return CResponse::demoCreateNewError('seller');
        }
        $data = [...$seller->toArray(), ...$request->all(),];

        $validated_data = CValidator::validate($data, Seller::rules($seller->id));
        $seller->fill($validated_data);
        $seller->saveAvatarImage($request);
        if ($request->has('password')) {
            $seller->password = Hash::make($request->get('password'));
        }

        $seller->save();
        return new StrictSellerResource($seller);

    }

    public function removeAvatar(Request $request): StrictSellerResource
    {
        $seller = $request->user();
        $seller->deleteAvatar();
        $seller->save();
        return new StrictSellerResource($seller);
    }


    /**
     * TODO: ----------- You need to setup your own business logic to delete, which data
     * @throws ValidationException
     */
    public function deleteAccount(Request $request): Application|ResponseFactory|Response
    {
        $customer = $request->user();
        $validated_data = CValidator::validate($request->all(), [
            'password' => 'required'
        ]);

        if (!Hash::check($validated_data['password'], $customer->password)) {
            return $this->validationErrorResponse(['password' => 'Password is wrong']);
        }

        // TODO: Here need to add -------------------
        return $this->errorResponse('account_deleted');
    }


}

