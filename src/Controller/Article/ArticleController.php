<?php

namespace App\Controller\Article;

use App\ControllerHandler\Article\ArticleControllerHandler;
use App\Entity\Article\Article;
use App\Entity\Article\Comment;
use App\Form\Article\ArticleType;
use App\Form\Article\CommentType;
use App\Repository\Article\ArticleRepository;
use App\Repository\Article\CommentRepository;
use App\Repository\User\UserRepository;
use Silversat\PermissionBundle\Security\PermissionChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        private readonly UserRepository $userRepository,
        private readonly ArticleRepository $articleRepository,
        private readonly CommentRepository $commentRepository
    ) {}

    #[Route(name: 'index')]
    public function index(
        ArticleRepository $articleRepository,
        Request $request
    ): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 9;

        $articles = $articleRepository->findPaginated($page, $limit);
        $totalArticles = $articleRepository->count(['publish' => true]);
        $hasNextPage = ($page * $limit) < $totalArticles;

        if ($request->headers->get('Turbo-Frame')) {
            return $this->render('article/_articles_list.html.twig', [
                'articles' => $articles,
                'page' => $page,
                'hasNextPage' => $hasNextPage,
            ]);
        }

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'page' => $page,
            'hasNextPage' => $hasNextPage
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
        if (!$session->has('id')) {
            return $this->redirectToRoute('article_index');
        }

        if (!$this->isAuthorized($request, "ROLE_ADMIN") && !($article->getAuthor()->getUserApiId() === $session->get('id'))) {
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
    public function show(
        Article $article,
        SessionInterface $session,
        Request $request
    ): Response
    {
        if (!$this->isAuthorized($request, "ROLE_AUTHOR") && !$article->isPublish()) {
            return $this->redirectToRoute('article_index');
        }

        if ($session->has('id')) {
            $user = $this->userRepository->findOneBy(['id' => $session->get('id')]);

            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);
        }

        $suggestedArticles = $this->articleRepository->findSuggested(3, $article->getId());

        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $comments = $this->commentRepository->findPaginatedByArticle($article, $page, $limit);
        $totalComments = $this->commentRepository->count(['article' => $article]);
        $hasNextPage = ($page * $limit) < $totalComments;

        if ($request->headers->get('Turbo-Frame')) {
            return $this->render('article/comment/_comments_list.html.twig', [
                'comments' => $comments,
                'hasNextPage' => $hasNextPage,
                'page' => $page,
                'article' => $article,
            ]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'user' => $user ?? null,
            'form' => isset($form) ? $form->createView() : null,
            'suggestedArticles' => $suggestedArticles,
            'comments' => $comments,
            'hasNextPage' => $hasNextPage,
            'page' => $page
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

    #[Route('/upload/picture', name: 'upload_picture', methods: ['POST'])]
    public function uploadPicture(Request $request): JsonResponse
    {
        $file = $request->files->get('file');

        if (!$file) {
            return new JsonResponse(['error' => 'No file was uploaded.'], Response::HTTP_BAD_REQUEST);
        }

        $filename = uniqid() . '.' . $file->guessExtension();

        $file->move(
            $this->getParameter('kernel.project_dir') . '/public/uploads/pictures/articles/content',
            $filename
        );

        return $this->json([
            'url' => '/uploads/pictures/articles/content/' . $filename,
        ]);
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
