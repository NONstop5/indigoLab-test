<?php

declare(strict_types=1);

namespace App\Dto\Response;

class PhoneCodeDto
{
    private string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
