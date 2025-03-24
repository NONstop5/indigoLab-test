<?php

declare(strict_types=1);

namespace App\Dto\Response;

class AuthDto
{
    private string $authText;
    private int $userId;

    public function __construct(string $authText, int $userId)
    {
        $this->authText = $authText;
        $this->userId = $userId;
    }

    public function getAuthText(): string
    {
        return $this->authText;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
