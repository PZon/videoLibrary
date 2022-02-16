<?php

namespace App\Repository;

use App\Entity\Videos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Videos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Videos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Videos[]    findAll()
 * @method Videos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideosRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Videos::class);
        $this->paginator = $paginator;
    }

    public function findByChildIds(array $val, int $page, ?string $sort_method){

        $sort_method = $sort_method !='rating' ? $sort_method: 'ASC';
        $dbquery = $this->createQueryBuilder('v')->andWhere('v.category IN (:val)')->setParameter('val', $val)
        ->orderBy('v.title', $sort_method)->getQuery();

        $pagination = $this->paginator->paginate($dbquery, $page,5);
        return $pagination;
    }

    public function findByTitle(string $query, int $page, ?string $sort_method){

        $sort_method = $sort_method !='rating' ? $sort_method: 'ASC';
        $queryBuilder = $this->createQueryBuilder('v');

        $searchTerms = $this->prepareQuery($query);

        foreach($searchTerms as $key=>$term){
            $queryBuilder->orWhere('v.title LIKE :t_'.$key)->setParameter('t_'.$key, '%'.trim($term).'%');
        }

        $dbquery=$queryBuilder->orderBy('v.title', $sort_method)->getQuery();

        $pagination = $this->paginator->paginate($dbquery, $page,5);
        return $pagination;
    }

    private function prepareQuery(string $query):array{
        return explode(' ', $query);
    }

    // /**
    //  * @return Videos[] Returns an array of Videos objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Videos
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
