<?php

namespace App\Exceptions;

use App\Helpers\CResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class InstallationFallbackException extends Exception
{
    public string $step;

    /**
     * @param $step
     */
    public function __construct($step)
    {
        parent::__construct();
        $this->step = $step;
    }


    public function render(): JsonResponse
    {
        return response()->json([
            'fallback' => $this->step,
        ], CResponse::$INSTALLATION_FALLBACK);
    }
}
