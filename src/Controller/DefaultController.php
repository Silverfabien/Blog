<?php

namespace App\Controller;

use App\ControllerHandler\DefaultControllerHandler;
use App\Entity\Contact\Contact;
use App\Form\Contact\ContactType;
use App\Repository\Article\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController extends AbstractController
{
    public function __construct(
        private readonly ParameterBagInterface $params
    ) {}

    #[Route('/', name: 'default')]
    public function index(
        SessionInterface $session,
        ArticleRepository $articleRepository,
        Request $request,
        DefaultControllerHandler $defaultControllerHandler
    ): Response
    {
        $contact = new Contact();

        $user = $this->decodeJwt($request);

        if ($user) {
            $contact->setEmail($user['user']['email']);
            $contact->setName($user['otherData']['lastname'].' '.$user['otherData']['firstname']);
        }

        $form = $this->createForm(ContactType::class, $contact, )->handleRequest($request);

        if ($defaultControllerHandler->contact($form, $contact)) {
            return $this->redirectToRoute('default');
        }

        $lastArticles = $articleRepository->findBy(
            ['publish' => true],
            ['publishAt' => 'DESC'],
            3
        );

        return $this->render('default/index.html.twig', [
            'session' => $session,
            'articles' => $lastArticles,
            'form' => $form->createView()
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
