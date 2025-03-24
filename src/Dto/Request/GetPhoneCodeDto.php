<?php

declare(strict_types=1);

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class GetPhoneCodeDto
{
    #[Assert\NotBlank(message: 'Phone number cannot be blank.')]
    #[Assert\Regex(
        pattern: '/^\+79\d{9}$/',
        message: 'Phone number must be in the format +79151234567'
    )]
    private string $phoneNumber;

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }
}
