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

       // $sort_method = $sort_method !='rating' ? $sort_method: 'ASC';

       if($sort_method != 'rating'){
         $dbquery = $this->createQueryBuilder('v')
         ->andWhere('v.category IN (:val)')
         ->leftJoin('v.comments','c')
         //->addSelect('c')
         ->leftJoin('v.usersThatLikes', 'l')
         ->leftJoin('v.usersThatDontLike', 'd')
         ->addSelect('c','l','d')
         ->setParameter('val', $val)
         ->orderBy('v.title', $sort_method);
       }else{
		   $dbquery = $this->createQueryBuilder('v')
		   ->addSelect('COUNT(l) AS HIDDEN likes') 
		   ->leftJoin('v.usersThatLikes','l')
		   ->andWhere('v.category IN (:val)')
		   ->setParameter('val', $val)
		   ->groupBy('v')
		   ->orderBy('likes', 'DESC');
	   }
		$dbquery->getQuery();
         $pagination = $this->paginator->paginate($dbquery, $page, Videos::perPage);
         return $pagination;
    }

    public function findByTitle(string $query, int $page, ?string $sort_method){

        //$sort_method = $sort_method !='rating' ? $sort_method: 'ASC';
        $queryBuilder = $this->createQueryBuilder('v');

        $searchTerms = $this->prepareQuery($query);

        foreach($searchTerms as $key=>$term){
            $queryBuilder->orWhere('v.title LIKE :t_'.$key)->setParameter('t_'.$key, '%'.trim($term).'%');
        }
		
		if(sort_method != 'rating'){
			$dbquery=$queryBuilder
			->orderBy('v.title', $sort_method)
			->leftJoin('v.comments','c')
			->leftJoin('v.usersThatLikes', 'l')
			->leftJoin('v.usersThatDontLike', 'd')
			->addSelect('c','l','d');		
		}else{
		   $dbquery = $this->createQueryBuilder('v')
		   ->addSelect('COUNT(l) AS HIDDEN likes', 'c') 
		   ->leftJoin('v.usersThatLikes','l')
		   ->leftJoin('v.comments','c')
		   ->groupBy('v','c')
		   ->orderBy('likes', 'DESC');
		}
			
			$dbquery->getQuery();
			$pagination = $this->paginator->paginate($dbquery, $page,Videos::perPage);
			return $pagination;
    }

    private function prepareQuery(string $query):array{
        return explode(' ', $query);
    }

    public function videoDetails($id){
        return $this->createQueryBuilder('v')
            ->leftJoin('v.comments','c')
            ->leftJoin('c.user','u')
            ->addSelect('c','u')
            ->where('v.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
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
