<?php

namespace App\ControllerHandler\Article;

use App\Entity\Article\Article;
use App\Entity\Article\Comment;
use App\Repository\Article\ArticleRepository;
use App\Repository\Article\CommentRepository;
use App\Repository\User\UserRepository;
use DateTimeImmutable;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

readonly class CommentControllerHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private CommentRepository $commentRepository
    ) {}

    public function new(
        FormInterface $form,
        Comment $comment,
        Article $article,
        SessionInterface $session
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy(['userApiId' => $session->get('id')]);

            $comment->setAuthor($user);
            $comment->setArticle($article);

            $this->commentRepository->create($comment);

            return true;
        }

        return false;
    }

    public function edit(
        FormInterface $form,
        Comment $comment
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUpdatedAt(new DateTimeImmutable());

            $this->commentRepository->update($comment);

            return true;
        }

        return false;
    }

    public function delete(Comment $comment): bool
    {
        $this->commentRepository->remove($comment);

        return true;
    }
}
