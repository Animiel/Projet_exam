<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Model\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
// use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Annonce>
 *
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Annonce::class);
    }

    public function save(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //get annonces recherchées par mot clé
    public function findBySearch(SearchData $searchData, int $page)
    {
        $annonces = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('App\Entity\Annonce', 'a')
            ->orderBy('a.publicationDate', 'DESC');

        if(!empty($searchData->q))  {
            $annonces = $annonces
            ->where('a.petName LIKE :q')
            ->setParameter('q', "%{$searchData->q}%");
        }
        if(!empty($searchData->local)) {
            $annonces = $annonces
            ->andWhere('a.localisation LIKE :local')
            ->setParameter('local', $searchData->local);
        }
        if(!empty($searchData->genre)) {
            $annonces = $annonces
            ->andWhere('a.petGenre LIKE :genre')
            ->setParameter('genre', $searchData->genre);
        }
        if(!empty($searchData->motif)) {
            $annonces = $annonces
            ->join('a.motifAnnonce', 'm')
            ->andWhere('m.id IN (:motif)')
            ->setParameter('motif', $searchData->motif);
        }

        $data = $this->paginator->paginate($annonces, $page, 2);

        return $data;
    }

    public function annoncesPaginated(int $page): PaginationInterface
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('App\Entity\Annonce', 'a')
            ->orderBy('a.publicationDate', 'DESC');

            $data = $this->paginator->paginate($query, $page, 2);

        return $data;
    }

//    /**
//     * @return Annonce[] Returns an array of Annonce objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Annonce
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
