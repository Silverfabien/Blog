<?php

namespace App\Repository\Article;

use App\Entity\Article\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function create($article): void
    {
        $this->getEntityManager()->persist($article);
        $this->getEntityManager()->flush();
    }

    public function update($article): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove($article): void
    {
        $this->getEntityManager()->remove($article);
        $this->getEntityManager()->flush();
    }

    public function findSuggested(int $limit, int $currentArticleId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id != :currentId')
            ->andWhere('a.publish = :publish')
            ->setParameter('currentId', $currentArticleId)
            ->setParameter('publish', true)
            ->orderBy('a.publishAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findPaginated(int $page, int $limit): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.publish = :publish')
            ->setParameter('publish', true)
            ->orderBy('a.publishAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
