<?php

namespace App\Controller\Article;

use App\ControllerHandler\Article\CommentControllerHandler;
use App\Entity\Article\Comment;
use App\Form\Article\CommentType;
use App\Repository\User\UserRepository;
use Silversat\PermissionBundle\Security\PermissionChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comment', name: 'comment_')]
final class CommentController extends AbstractController
{
    public function __construct(
        private readonly PermissionChecker $permissionChecker,
        private readonly UserRepository $userRepository,
        private readonly CommentControllerHandler $commentControllerHandler
    ) {}

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(
        Request $request,
        Comment $comment,
        SessionInterface $session
    ): Response
    {
        if (!$session->has('id') || !$this->asPermission($session, $request, $comment)) {
            return $this->redirectToRoute('default');
        }

        $form = $this->createForm(CommentType::class, $comment)->handleRequest($request);
        if ($this->commentControllerHandler->edit($form, $comment)) {
            return $this->redirectToRoute('article_show', ['slug' => $comment->getArticle()->getSlug()]);
        }

        return $this->render('article/comment/_edit_form.html.twig', [
            'comment' => $comment,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete')]
    public function delete(
        Request $request,
        Comment $comment,
        SessionInterface $session
    ): Response
    {
        if (!$session->has('id') || !$this->asPermission($session, $request, $comment)) {
            return $this->redirectToRoute('default');
        }


        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            $commentId = $comment->getId();

            $this->commentControllerHandler->delete($comment);

            if ($request->getPreferredFormat() === 'turbo_stream') {
                return $this->render('article/comment/delete.turbo_stream.twig', [
                    'comment_id' => $commentId,
                ], new Response('', Response::HTTP_OK, ['Content-Type' => 'text/vnd.turbo-stream.html']));
            }
        }

        return $this->redirectToRoute('article_show', ['slug' => $comment->getArticle()->getSlug()]);
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
        Comment $comment
    ): bool {
        $userId = $session->get('id');
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        $isAuthor = $this->isAuthorized($request, "ROLE_AUTHOR");
        $isOwner = ($user->getId() === $comment->getAuthor()->getId());
        $isAdmin = $this->isAuthorized($request, "ROLE_ADMIN");

        if (!$isAdmin && (!$isAuthor || !$isOwner)) {
            return false;
        }

        return true;
    }
}
