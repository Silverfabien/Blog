<?php

namespace App\ControllerHandler\Article;

use App\Entity\Article\Article;
use App\Entity\Article\ArticleLike;
use App\Entity\User\User;
use App\Repository\Article\ArticleLikeRepository;
use App\Repository\Article\ArticleRepository;
use App\Repository\User\UserRepository;
use DateTimeImmutable;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

readonly class ArticleControllerHandler
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private UserRepository $userRepository,
        private ArticleLikeRepository $articleLikeRepository
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

    public function see(Article $article): bool
    {
        $article->setSee($article->getSee() + 1);

        $this->articleRepository->update($article);

        return true;
    }

    public function like(User $user, Article $article, ArticleLike $articleLike): bool
    {
        $article->setLikeCount($article->getLikeCount() + 1);
        $this->articleRepository->update($article);

        $articleLike->setUser($user);
        $articleLike->setArticle($article);

        $this->articleLikeRepository->create($articleLike);

        return true;
    }

    public function unlike(Article $article, ArticleLike $articleLike): bool
    {
        $article->setLikeCount($article->getLikeCount() - 1);
        $this->articleRepository->update($article);

        $this->articleLikeRepository->remove($articleLike);

        return true;
    }
}
