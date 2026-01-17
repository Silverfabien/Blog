<?php

namespace App\ControllerHandler\Article;

use App\Entity\Article\Article;
use App\Repository\Article\ArticleRepository;
use App\Repository\User\UserRepository;
use DateTimeImmutable;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

readonly class ArticleControllerHandler
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private UserRepository $userRepository
    ) {}

    public function new(
        FormInterface $form,
        Article $article,
        SessionInterface $session
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy(['userApiId' => $session->get('id')]);
            if ($article->isPublish()) {
                $article->setPublishAt(new DateTimeImmutable());
            }

            $article->setAuthor($user);

            $this->articleRepository->create($article);

            return true;
        }

        return false;
    }

    public function edit(
        FormInterface $form,
        Article $article,
        SessionInterface $session
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy(['userApiId' => $session->get('id')]);

            $article->setAuthorEdit($user);
            $article->setUpdatedAt(new DateTimeImmutable());

            $this->articleRepository->update($article);

            return true;
        }

        return false;
    }

    public function delete(Article $article): bool
    {
        $this->articleRepository->remove($article);

        return true;
    }
}
