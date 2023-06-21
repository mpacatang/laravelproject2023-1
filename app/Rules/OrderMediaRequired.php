<?php

namespace App\Rules;

use App\Models\Shop;
use Illuminate\Contracts\Validation\Rule;

class OrderMediaRequired implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public int $shopId;

    public function __construct(int $shopId)
    {
        $this->shopId = $shopId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $shop = Shop::findOrFail($this->shopId);
        if (!$shop->need_prescription)
            return true;
        if (is_array($value) && count($value) > 0) return true;
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Please attach prescription as per requirement';
    }
}
