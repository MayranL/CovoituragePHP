<?php


namespace App\Repository;

use App\Entity\Annonce;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
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

    /**
     * Récupère toutes les annonces dont la date est dépassée et associées à un utilisateur
     *
     * @param int $userId L'ID de l'utilisateur
     * @return Annonce[] La liste des annonces correspondantes
     */
    public function findExpiredByUser($userId): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.reservations', 'r', Join::WITH, 'r.passager = :userId')
            ->andWhere('a.date < :currentDate')
            ->andWhere('r.passager = :userId')
            ->setParameter('currentDate', new \DateTime())
            ->setParameter('userId', $userId)
            ->getQuery();

        return $qb->getResult();
    }

    /**
     * Récupère toutes les annonces dont la date est dépassée et associées à un utilisateur
     *
     * @return Annonce[] La liste des annonces correspondantes
     */
    public function findallNotOld(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('not a.date < :currentDate')
            ->setParameter('currentDate', new \DateTime())
            ->getQuery();

        return $qb->getResult();
    }

    public function findByDeparture($departure)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.villeDepart = :departure')
            ->setParameter('departure', $departure)
            ->getQuery()
            ->getResult();
    }

    public function findByArrival($arrival)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.villeArrive = :arrival')
            ->setParameter('arrival', $arrival)
            ->getQuery()
            ->getResult();
    }

    public function findByHour($hour)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.hour = :hour')
            ->setParameter('hour', $hour)
            ->getQuery()
            ->getResult();
    }

    public function findByDepartureAndArrival($departure, $arrival)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.villeDepart = :departure')
            ->andWhere('a.villeArrive = :arrival')
            ->setParameters(['departure' => $departure, 'arrival' => $arrival])
            ->getQuery()
            ->getResult();
    }

    public function findByDepartureArrivalAndHour($departure, $arrival, $hour)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.villeDepart = :departure')
            ->andWhere('a.villeArrive = :arrival')
            ->andWhere('a.hour = :hour')
            ->setParameters(['departure' => $departure, 'arrival' => $arrival, 'hour' => $hour])
            ->getQuery()
            ->getResult();
    }


}
