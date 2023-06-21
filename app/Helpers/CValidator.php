<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CValidator
{
    /**
     * @throws ValidationException
     */
    static function validate(array $data, array $rules, array $messages = [], array $customAttributes = [], $errorPrefix = null): array
    {
        $validator = Validator::make($data, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            $error = $validator->errors()->toArray();
            if ($errorPrefix) {
                $errors = [];
                foreach ($validator->errors()->messages() as $key => $value) {
                    $errors[$errorPrefix.$key] = is_array($value) ? implode(',', $value) : $value;
                }
                throw ValidationException::withMessages($errors);

            }
            throw ValidationException::withMessages($error);

//            throw CustomResponse::validation_error($validator->errors())
        }
        return $validator->validated();
    }
}
