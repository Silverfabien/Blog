<?php

namespace App\Controller\Admin;

use App\Repository\Article\ArticleRepository;
use App\Repository\Article\CommentRepository;
use App\Repository\Contact\ContactRepository;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
final class DashboardController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly CommentRepository $commentRepository,
        private readonly UserRepository $userRepository,
        private readonly ContactRepository $contactRepository
    ) {}

    #[Route('/', name: 'dashboard')]
    public function index(SessionInterface $session): Response
    {
        $user = $this->userRepository->findOneBy(['userApiId' => $session->get('id')]);

        return $this->render('admin/dashboard/index.html.twig', [
            'countArticle' => $this->articleRepository->count(),
            'countComment' => $this->commentRepository->count(),
            'countUser' => $this->userRepository->count(),
            'countContact' => $this->contactRepository->count(),
            'countArticleLastDay' => $this->articleRepository->countLastDay(),
            'countCommentLastDay' => $this->commentRepository->countLastDay(),
            'countContactLastDay' => $this->contactRepository->countLastDay(),
            'countUserLastDay' => $this->userRepository->countLastDay(),
            'countArticleLast7Days' => $this->articleRepository->countLast7Days(),
            'countCommentLast7Days' => $this->commentRepository->countLast7Days(),
            'countContactLast7Days' => $this->contactRepository->countLast7Days(),
            'countUserLast7Days' => $this->userRepository->countLast7Days(),
            'user' => $user
        ]);
    }
}
