<?php

namespace App\Controller;

use App\Dto\Request\GetPhoneCodeDto;
use App\Service\PhoneVerificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route(
        '/user/request-code',
        name: 'user_request_code',
        methods: ['POST'])
    ]
    public function requestCode(
        #[MapRequestPayload] GetPhoneCodeDto $requestDto,
        PhoneVerificationService $verificationService,
    ): JsonResponse {
        $phoneCodeDto = $verificationService->getPhoneCode($requestDto);

        return $this->json($phoneCodeDto);
    }

    //    #[Route('/verify-code', methods: ['POST'])]
    //    public function verifyCode(Request $request): JsonResponse
    //    {
    //        $phoneNumber = $request->request->get('phone_number');
    //        $code = $request->request->get('code');
    //
    //        try {
    //            $user = $this->verificationService->verifyCode($phoneNumber, $code);
    //            return $this->json(['success' => true, 'user_id' => $user->getId()]);
    //        } catch (\Exception $e) {
    //            return $this->json(['error' => $e->getMessage()], 400);
    //        }
    //    }
}
