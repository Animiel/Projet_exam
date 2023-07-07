<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Model\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
    public function __construct(ManagerRegistry $registry)
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
    public function findBySearch(SearchData $searchData)
    {
        $annonces = $this->createQueryBuilder('a')
            ->addOrderBy('a.publicationDate', 'DESC');

        if(!empty($searchData->q) || !empty($searchData->local) || (!empty($searchData->genre)))  {
            $annonces = $annonces
            ->where('a.petName LIKE :q')
            ->andWhere('a.localisation LIKE :local')
            ->andWhere('a.petGenre = :genre')
            ->setParameters([
                'q' => "%{$searchData->q}%",
                'local' => "%{$searchData->local}%",
                'genre' => $searchData->genre,
            ]);
        }
        if(!empty($searchData->motif)) {
            $annonces = $annonces
            ->join('a.motifAnnonce', 'm')
            ->andWhere('m.id IN (:motif)')
            ->setParameter('motif', $searchData->motif);
                
        }

        $annonces = $annonces->getQuery()->getResult();

        return $annonces;
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
