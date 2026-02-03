<?php

namespace App\Controller\Admin\Article;

use App\ControllerHandler\Admin\Article\ArticleControllerHandler;
use App\Entity\Article\Article;
use App\Repository\Article\ArticleRepository;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/article', name: 'admin_article_')]
final class ArticleController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ArticleRepository $articleRepository,
        private readonly ArticleControllerHandler $articleControllerHandler
    ) {}

    #[Route('/', name: 'index')]
    public function index(SessionInterface $session): Response
    {
        $user = $this->userRepository->findOneBy(['userApiId' => $session->get('id')]);
        $articles = $this->articleRepository->findAll();

        return $this->render('admin/article/index.html.twig', [
            'user' => $user,
            'articles' => $articles
        ]);
    }

    #[Route('/{slug}/remove', name: 'delete')]
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getSlug(), $request->getPayload()->getString('_token'))) {
            $this->articleControllerHandler->delete($article);
        }

        return $this->redirectToRoute('admin_article_index');
    }
}
