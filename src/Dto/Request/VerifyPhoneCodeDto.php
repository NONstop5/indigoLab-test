<?php

declare(strict_types=1);

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class VerifyPhoneCodeDto
{
    #[Assert\NotBlank(message: 'Phone number cannot be blank')]
    #[Assert\Regex(
        pattern: '/^\+79\d{9}$/',
        message: 'Phone number must be in the format +79151234567'
    )]
    private string $phoneNumber;

    #[Assert\NotBlank(message: 'Phone code cannot be blank')]
    #[Assert\Regex(
        pattern: '/^\d{4}$/',
        message: 'Phone code must be in the format "1234"'
    )]
    private string $phoneCode;

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getPhoneCode(): string
    {
        return $this->phoneCode;
    }

    public function setPhoneCode(string $phoneCode): void
    {
        $this->phoneCode = $phoneCode;
    }
}
