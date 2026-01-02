<?php

namespace App\Controller\Article;

use App\ControllerHandler\Article\ArticleControllerHandler;
use App\Entity\Article\Article;
use App\Form\Article\ArticleType;
use App\Repository\Article\ArticleRepository;
use App\Repository\User\UserRepository;
use Silversat\PermissionBundle\Security\PermissionChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/article', name: 'article_')]
final class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleControllerHandler $articleControllerHandler,
        private readonly PermissionChecker $permissionChecker,
        private readonly UserRepository $userRepository
    ) {}

    #[Route(name: 'index')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, SessionInterface $session): Response
    {
        if (!$this->isAuthorized($request, "ROLE_AUTHOR")) {
            return $this->redirectToRoute('default');
        }

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article)->handleRequest($request);

        if ($this->articleControllerHandler->new($form, $article, $session)) {
            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/edit', name: 'edit')]
    public function edit(Request $request, Article $article, SessionInterface $session): Response
    {
        if (!$session->has('id') || !$this->asPermission($session, $request, $article)) {
            return $this->redirectToRoute('default');
        }

        $form = $this->createForm(ArticleType::class, $article)->handleRequest($request);

        if ($this->articleControllerHandler->edit($form, $article, $session)) {
            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'show')]
    public function show(Article $article, SessionInterface $session): Response
    {
        if ($session->has('id')) {
            $user = $this->userRepository->findOneBy(['id' => $session->get('id')]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'user' => $user ?? null
        ]);
    }

    #[Route('/{slug}/remove', name: 'delete')]
    public function delete(Request $request, Article $article, SessionInterface $session): Response
    {
        if (!$session->has('id') || !$this->asPermission($session, $request, $article)) {
            return $this->redirectToRoute('default');
        }

        if ($this->isCsrfTokenValid('delete'.$article->getSlug(), $request->getPayload()->getString('_token'))) {
            $this->articleControllerHandler->delete($article);
        }

        return $this->redirectToRoute('article_index');
    }

    private function isAuthorized(Request $request, string $attempt): bool
    {
        $jwt = $request->cookies->get('jwt_token');

        if (!$jwt) {
            return false;
        }

        $jwtDecode = json_decode(base64_decode(explode(".", $jwt)[1]), true);

        return $this->permissionChecker->isPermissionGranted($jwtDecode, $attempt);
    }

    private function asPermission(
        SessionInterface $session,
        Request $request,
        Article $article
    ): bool {
        $userId = $session->get('id');
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        $isAuthor = $this->isAuthorized($request, "ROLE_AUTHOR");
        $isOwner = ($user->getId() === $article->getAuthor()->getId());
        $isAdmin = $this->isAuthorized($request, "ROLE_ADMIN");

        if (!$isAdmin && (!$isAuthor || !$isOwner)) {
            return false;
        }

        return true;
    }
}
