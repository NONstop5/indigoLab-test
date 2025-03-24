<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Request\GetPhoneCodeDto;
use App\Dto\Response\PhoneCodeDto;
use App\Entity\PhoneVerificationCode;
use App\Entity\User;
use App\Repository\PhoneVerificationCodeRepository;
use Doctrine\ORM\EntityManagerInterface;

class PhoneVerificationService
{
    private const CODES_COUNT_LIMIT = 3;
    private const CODES_COUNT_LIMIT_PERIOD = '15 minutes';

    private EntityManagerInterface $em;
    private PhoneVerificationCodeRepository $codeRepository;

    public function __construct(
        EntityManagerInterface $em,
        PhoneVerificationCodeRepository $codeRepository,
    ) {
        $this->em = $em;
        $this->codeRepository = $codeRepository;
    }

    /**
     * @throws \Exception
     */
    public function getPhoneCode(GetPhoneCodeDto $getPhoneCodeDto): PhoneCodeDto
    {
        $phoneNumber = $getPhoneCodeDto->getPhoneNumber();

        if ($this->isBlockedPhoneNumber($phoneNumber)) {
            // TODO: Будет лучше сделать свое кастомное более специфичное исключение и выкидывать его
            throw new \Exception('Номер заблокирован');
        }

        $this->checkRequestLimit($phoneNumber);

        $lastCodeData = $this->codeRepository->getLastCode($phoneNumber);

        if ($lastCodeData !== false && $this->isActualExistedCode($lastCodeData)) {
            /** @var string $lastCode */
            $lastCode = $lastCodeData['code'];

            return new PhoneCodeDto($lastCode);
        }

        $newVerificationCode = $this->generateCode();

        $this->createPhoneVerificationCode($phoneNumber, $newVerificationCode);

        return new PhoneCodeDto($newVerificationCode);
    }

    private function isBlockedPhoneNumber(string $phoneNumber): bool
    {
        /*
         * TODO:
         *  Можно создать еще одну таблицу и сохранять в нее заблокированные номера, чтобы потом их проверять,
         *  но кажется это не очень, поэтому даже не стал создавать эту таблицу
         *  Лучше и быстрее будет сохранять в redis ключ со сроком жизни 1 час и просто проверять существование этого ключа
         *  ключ вида 'user_phone_+7910123456789'
         */
        // $block = $this->blockRepository->isPhoneNumberBlocked($phoneNumber);

        return false;
    }

    /**
     * @throws \Exception
     */
    private function checkRequestLimit(string $phoneNumber): void
    {
        $recentDateTime = new \DateTimeImmutable('-' . self::CODES_COUNT_LIMIT_PERIOD);

        $recentCodesCount = $this->codeRepository->getRecentCodesCount($phoneNumber, $recentDateTime);

        if ($recentCodesCount >= self::CODES_COUNT_LIMIT) {
            $this->blockPhoneNumber($phoneNumber);

            // TODO: Будет лучше сделать свое кастомное более специфичное исключение и выкидывать его
            throw new \Exception('Номер заблокирован');
        }
    }

    private function blockPhoneNumber(string $phoneNumber): void
    {
        // TODO:
        //  Тут создаем или запись в таблице БД с заблокированными номерами (нежелательно)
        //  или создаем ключ в redis (предпочтительнее) вида 'user_phone_+7910123456789'
    }

    /**
     * @param array<string, mixed> $code
     *
     * @throws \Exception
     */
    private function isActualExistedCode(array $code): bool
    {
        /** @var string $codeCreatedAt */
        $codeCreatedAt = $code['created_at'];

        // TODO:
        //  Возникли проблемы с временем, пришлось сделать костыль с timezone,
        //  надо будет с этим разобраться. Возможно в docker контейнере все наладится
        $createdAt = new \DateTimeImmutable($codeCreatedAt, new \DateTimeZone('Europe/Moscow'));
        $now = new \DateTimeImmutable('-1 minute', new \DateTimeZone('Europe/Moscow'));

        return $createdAt > $now;
    }

    private function generateCode(): string
    {
        return mb_str_pad((string) random_int(0, 9999), 4, '0', \STR_PAD_LEFT);
    }

    private function createPhoneVerificationCode(string $phoneNumber, string $newVerificationCode): void
    {
        $verificationCode = new PhoneVerificationCode();
        $verificationCode->setPhoneNumber($phoneNumber);
        $verificationCode->setCode($newVerificationCode);

        $this->em->persist($verificationCode);
        $this->em->flush();
    }
}
