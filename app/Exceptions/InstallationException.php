<?php

namespace App\Exceptions;

use App\Helpers\CResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class InstallationException extends Exception
{
    public string $error;
    public string $details;

    /**
     * @param $error
     * @param $details
     */
    public function __construct($error, $details)
    {
        parent::__construct();
        $this->error = $error;
        $this->details = $details;
    }


    public function render(): JsonResponse
    {
        return response()->json([
            'error' => $this->error,
            'details'=>$this->details
        ], CResponse::$INSTALLATION_ERROR);
    }
}
