<?php

namespace App\Controller\Security;

use App\DTO\Security\UserEditDTO;
use App\Form\Security\ResetPasswordType;
use App\Form\Security\UserEditType;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    public function __construct(
        private readonly ParameterBagInterface $params,
        private readonly UserRepository $userRepository
    ){}

    #[Route('/account', name: 'account')]
    public function account(Request $request): Response
    {
        $userInfo = $this->decodeJwt($request);

        if ($userInfo === []) {
            return $this->redirectToRoute('login');
        }

        $userDto = UserEditDTO::fromJwtData($userInfo);

        $userForm = $this->createForm(UserEditType::class, $userDto)->handleRequest($request);
        $resetPasswordForm = $this->createForm(ResetPasswordType::class)->handleRequest($request);

        $user = $this->userRepository->findOneBy(['id' => $userInfo['user']['id']]);

        return $this->render('security/account.html.twig', [
            'userForm' => $userForm->createView(),
            'resetPasswordForm' => $resetPasswordForm->createView(),
            'user' => $user,
            'userInfo' => $userInfo['otherData']
        ]);
    }

    private function decodeJwt(Request $request): array
    {
        $jwt = $request->cookies->get('jwt_token');
        $passphrase = $this->params->get('check_pass');

        if (!$jwt) {
            return [];
        }

        $decodeJwt = json_decode(base64_decode(explode(".", $jwt)[1]), true);
        $iv = base64_decode($decodeJwt['iv']);
        $otherData = base64_decode($decodeJwt['other']);
        $decodeOtherData = openssl_decrypt($otherData, 'aes-256-cbc', $passphrase, 0, $iv);
        $jsonDecodeOtherData = json_decode($decodeOtherData, true);
        return ['user' => $decodeJwt, 'otherData' => $jsonDecodeOtherData];
    }
}
