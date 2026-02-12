<?php

namespace App\ControllerHandler\Admin\Article;

use App\Entity\Article\Article;
use App\Repository\Article\ArticleRepository;

readonly class ArticleControllerHandler
{
    public function __construct(
        private ArticleRepository $articleRepository
    ) {}

    public function delete(Article $article): bool
    {
        $this->articleRepository->remove($article);

        return true;
    }
}
