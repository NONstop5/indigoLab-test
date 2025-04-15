<?php

namespace App\Controller;

use App\Dto\Request\RequestPhoneCodeDto;
use App\Dto\Request\VerifyPhoneCodeDto;
use App\Service\PhoneVerificationService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route(
        '/request-code',
        name: 'request_code',
        methods: ['POST'])
    ]
    public function requestCode(
        #[MapRequestPayload] RequestPhoneCodeDto $requestDto,
        PhoneVerificationService $verificationService,
    ): JsonResponse {
        $phoneCodeDto = $verificationService->getPhoneCode($requestDto);

        return $this->json($phoneCodeDto);
    }

    /**
     * @throws Exception
     */
    #[Route('/verify-code', methods: ['POST'])]
    public function verifyCode(
        #[MapRequestPayload] VerifyPhoneCodeDto $requestDto,
        PhoneVerificationService $verificationService,
    ): JsonResponse {
        $authDto = $verificationService->verifyCode(
            $requestDto->getPhoneNumber(),
            $requestDto->getPhoneCode()
        );

        return $this->json($authDto);
    }
}
