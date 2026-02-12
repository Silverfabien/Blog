<?php

namespace App\ControllerHandler\Admin\Article;

use App\Entity\Article\Comment;
use App\Repository\Article\CommentRepository;

readonly class CommentControllerHandler
{
    public function __construct(
        private CommentRepository $commentRepository
    ) {}

    public function delete(Comment $comment): bool
    {
        $this->commentRepository->remove($comment);

        return true;
    }
}
