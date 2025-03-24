<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PhoneVerificationCodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhoneVerificationCodeRepository::class)]
class PhoneVerificationCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 50)]
    private string $phoneNumber;

    #[ORM\Column(length: 10)]
    private string $code;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $attempts = 0;

    #[ORM\Column]
    private bool $isUsed = false;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    private function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function setAttempts(int $attempts): static
    {
        $this->attempts = $attempts;

        return $this;
    }

    public function isUsed(): bool
    {
        return $this->isUsed;
    }

    public function setIsUsed(bool $isUsed): static
    {
        $this->isUsed = $isUsed;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Фабричный метод в сущности, как написано в задании, если я правильно понял.
     */
    public static function create(
        string $phoneNumber,
        string $code,
        int $attempts = 0,
        bool $isUsed = false,
    ): self {
        return (new self())
            ->setPhoneNumber($phoneNumber)
            ->setCode($code)
            ->setAttempts($attempts)
            ->setIsUsed($isUsed);
    }
}
