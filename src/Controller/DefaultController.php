<?php

namespace App\Controller;

use App\ControllerHandler\DefaultControllerHandler;
use App\Entity\Contact\Contact;
use App\Form\Contact\ContactType;
use App\Repository\Article\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController extends AbstractController
{
    #[Route('/', name: 'default')]
    public function index(
        SessionInterface $session,
        ArticleRepository $articleRepository,
        Request $request,
        DefaultControllerHandler $defaultControllerHandler
    ): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact)->handleRequest($request);

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
}
