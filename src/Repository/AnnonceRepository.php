<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Model\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

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
    public function findBySearch(SearchData $searchData, int $page, int $limit = 20)
    {
        $limit = abs($limit);
        $result = [];

        $annonces = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('App\Entity\Annonce', 'a')
            ->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit)
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

        $paginator = new Paginator($annonces);
        $data = $paginator->getQuery()->getResult();

        if(empty($data)) {
            return $result;
        }

        $nbr_pages = ceil($paginator->count() / $limit);

        $result['data'] = $data;
        $result['pages'] = $nbr_pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
    }

    //page = page actuelle, si limit non définie alors 6 résultats renvoyés par page.
    public function annoncesPaginated(int $page, int $limit = 20): array
    {
        //pour que limit soit toujours positive on prend la valeur absolue.
        $limit = abs($limit);
        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('App\Entity\Annonce', 'a')
            ->setMaxResults($limit)
            //le premier résultat de la page correspond à la page actuelle * la limite - la limite, puisqu'on affiche autant d'objets sur une page jusqu'a atteindre la limite donc en commençant la page suivante on lui retire le nombre d'annonces déjà affichées.
            ->setFirstResult(($page * $limit) - $limit)
            ->orderBy('a.publicationDate', 'DESC');

            $paginator = new Paginator($query);
            $data = $paginator->getQuery()->getResult();

            //on vérifie qu'on a des données
            if(empty($data)) {
                return $result;
            }

            //on calcule le nombre de pages
            //ceil = ceiling = arrondi supérieur
            $nbr_pages = ceil($paginator->count() / $limit);

            //on remplit le tableau
            $result['data'] = $data;
            $result['pages'] = $nbr_pages;
            $result['page'] = $page;
            $result['limit'] = $limit;

        return $result;
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
