<?php

namespace App\Repository\Article;

use App\Entity\Article\ArticleLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ArticleLike>
 */
class ArticleLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleLike::class);
    }

    public function create($articleLike): void
    {
        $this->getEntityManager()->persist($articleLike);
        $this->getEntityManager()->flush();
    }

    public function remove($articleLike): void
    {
        $this->getEntityManager()->remove($articleLike);
        $this->getEntityManager()->flush();
    }
}
