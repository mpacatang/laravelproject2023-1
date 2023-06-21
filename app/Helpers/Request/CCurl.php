<?php

namespace App\Helpers\Request;

class CCurl
{

    public int $status_code;
    public ?string $data, $error;

    /**
     * @param int $status_code
     * @param ?string $data
     * @param ?string $error
     */
    public function __construct(int $status_code, ?string $data, ?string $error)
    {
        $this->status_code = $status_code;
        $this->data = $data;
        $this->error = $error;
    }

    public function toString(): string
    {
        return '{$status:' . $this->status_code . ",data:" . $this->data . ",error:" . $this->error . '}';
    }

    function is_success(): bool
    {
        return $this->status_code >= 200 && $this->status_code < 300;
    }
}