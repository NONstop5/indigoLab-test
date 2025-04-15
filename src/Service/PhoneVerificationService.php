<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Request\RequestPhoneCodeDto;
use App\Dto\Response\AuthDto;
use App\Dto\Response\PhoneCodeDto;
use App\Entity\PhoneVerificationCode;
use App\Entity\User;
use App\Repository\PhoneVerificationCodeRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Random\RandomException;

class PhoneVerificationService
{
    private const CODES_COUNT_LIMIT = 3;
    private const CODES_COUNT_LIMIT_PERIOD = '15 minutes';
    private const PHONE_NUMBER_BLOCK_PERIOD_SEC = 3600;
    public RedisService $redisService;

    private EntityManagerInterface $em;
    private PhoneVerificationCodeRepository $codeRepository;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $em,
        PhoneVerificationCodeRepository $codeRepository,
        UserRepository $userRepository,
        RedisService $redisService,
    ) {
        $this->em = $em;
        $this->codeRepository = $codeRepository;
        $this->userRepository = $userRepository;
        $this->redisService = $redisService;
    }

    /**
     * @throws Exception
     */
    public function getPhoneCode(RequestPhoneCodeDto $getPhoneCodeDto): PhoneCodeDto
    {
        $phoneNumber = $getPhoneCodeDto->getPhoneNumber();

        if ($this->isBlockedPhoneNumber($phoneNumber)) {
            // TODO: Будет лучше сделать свое кастомное более специфичное исключение и выкидывать его
            throw new Exception('Номер заблокирован');
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
        return $this->redisService->has(
            sprintf(
                'user_phone_%s_blocked',
                $phoneNumber
            )
        );
    }

    /**
     * @throws Exception
     */
    private function checkRequestLimit(string $phoneNumber): void
    {
        $recentDateTime = new DateTimeImmutable('-' . self::CODES_COUNT_LIMIT_PERIOD);

        $recentCodesCount = $this->codeRepository->getRecentCodesCount($phoneNumber, $recentDateTime);

        if ($recentCodesCount >= self::CODES_COUNT_LIMIT) {
            $this->blockPhoneNumber($phoneNumber);

            // TODO: Будет лучше сделать свое кастомное более специфичное исключение и выкидывать его
            throw new Exception('Номер заблокирован');
        }
    }

    private function blockPhoneNumber(string $phoneNumber): void
    {
        $this->redisService->set(
            sprintf(
                'user_phone_%s_blocked',
                $phoneNumber
            ),
            1,
            self::PHONE_NUMBER_BLOCK_PERIOD_SEC
        );
    }

    /**
     * @param array<string, mixed> $codeData
     *
     * @throws Exception
     */
    private function isActualExistedCode(array $codeData): bool
    {
        /** @var string $codeCreatedAt */
        $codeCreatedAt = $codeData['created_at'];

        // TODO:
        //  Возникли проблемы с временем, пришлось сделать костыль с timezone,
        //  надо будет с этим разобраться. Возможно в docker контейнере все наладится
        $createdAt = new DateTimeImmutable($codeCreatedAt);
        $now = new DateTimeImmutable('-1 minute');

        return $createdAt > $now;
    }

    /**
     * @throws RandomException
     */
    private function generateCode(): string
    {
        return mb_str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    private function createPhoneVerificationCode(string $phoneNumber, string $newVerificationCode): void
    {
        $verificationCode = PhoneVerificationCode::create(
            $phoneNumber,
            $newVerificationCode
        );

        $this->em->persist($verificationCode);
        $this->em->flush();
    }

    /**
     * @throws Exception
     */
    public function verifyCode(string $phoneNumber, string $code): AuthDto
    {
        $lastCodeData = $this->codeRepository->getLastCode($phoneNumber);

        if ($lastCodeData === false) {
            throw new Exception('Код не найден');
        }

        if (!$this->isActualExistedCode($lastCodeData)) {
            throw new Exception('Код истек, запросите новый');
        }

        /** @var PhoneVerificationCode $verificationCode */
        $verificationCode = $this->codeRepository->find($lastCodeData['id']);

        if ($lastCodeData['is_used']) {
            throw new Exception('Код уже использован');
        }

        if ($lastCodeData['code'] !== $code) {
            throw new Exception('Код неверный');
        }

        $user = $this->userRepository->findOneBy(['phoneNumber' => $lastCodeData['phone_number']]);

        if ($user !== null) {
            return new AuthDto('Authorise success', $user->getId());
        }

        /*
         * Тут, думаю, стоит использовать транзакцию,
         * чтобы не получилось, что юзер создался, а код не пометился как использованный
         */
        $this->em->beginTransaction();

        try {
            $user = User::create($phoneNumber);
            $this->em->persist($user);

            $verificationCode->setIsUsed(true);

            $this->em->flush();
            $this->em->commit();
        } catch (Exception $e) {
            $this->em->rollback();

            throw $e;
        }

        return new AuthDto('Registration success', $user->getId());
    }
}
