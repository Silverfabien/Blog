<?php

namespace App\Repository\Article;

use App\Entity\Article\Article;
use App\Entity\Article\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function create($comment): void
    {
        $this->getEntityManager()->persist($comment);
        $this->getEntityManager()->flush();
    }

    public function update($comment): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove($comment): void
    {
        $this->getEntityManager()->remove($comment);
        $this->getEntityManager()->flush();
    }

    public function findPaginatedByArticle(Article $article, int $page, int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.article = :article')
            ->setParameter('article', $article)
            ->orderBy('c.createdAt', 'ASC') // On garde l'ordre chronologique
            ->setFirstResult(($page - 1) * $limit) // Offset : d'où on commence
            ->setMaxResults($limit) // Combien on en prend
            ->getQuery()
            ->getResult();
    }
}
