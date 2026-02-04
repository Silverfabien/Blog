<?php

namespace App\Controller\Admin\Article;

use App\ControllerHandler\Admin\Article\CommentControllerHandler;
use App\Entity\Article\Comment;
use App\Repository\Article\CommentRepository;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/comment', name: 'admin_comment_')]
final class CommentController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CommentRepository $commentRepository,
        private readonly CommentControllerHandler $commentControllerHandler
    ) {}

    #[Route('/', name: 'index')]
    public function index(SessionInterface $session): Response
    {
        $user = $this->userRepository->findOneBy(['userApiId' => $session->get('id')]);
        $comments = $this->commentRepository->findAll();

        return $this->render('admin/article/comment/index.html.twig', [
            'user' => $user,
            'comments' => $comments
        ]);
    }

    #[Route('/{id}/remove', name: 'delete')]
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            $this->commentControllerHandler->delete($comment);
        }

        return $this->redirectToRoute('admin_comment_index');
    }
}
