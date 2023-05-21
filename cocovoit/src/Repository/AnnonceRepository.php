<?php


namespace App\Repository;

use App\Entity\Annonce;

use App\Entity\User;
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
            ->andWhere('a.nbplace > 0')
            ->setParameter('currentDate', new \DateTime())
            ->getQuery();

        return $qb->getResult();
    }

    public function findByDeparture($departure)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.villeDepart LIKE :departure')
            ->andWhere('not a.date < :currentDate')
            ->setParameter('currentDate', new \DateTime())
            ->setParameter('departure', '%' . $departure . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByArrival($arrival)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.villeArrive LIKE :arrival')
            ->andWhere('not a.date < :currentDate')
            ->setParameter('currentDate', new \DateTime())
            ->setParameter('arrival', '%' . $arrival . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByDepartureAndArrival($departure, $arrival)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.villeDepart LIKE :departure')
            ->andWhere('a.villeArrive LIKE :arrival')
            ->andWhere('not a.date < :currentDate')
            ->setParameter('currentDate', new \DateTime())
            ->setParameters(['departure' => '%' . $departure . '%', 'arrival' => '%' . $arrival . '%'])
            ->getQuery()
            ->getResult();
    }

    public function findByDepartureAndHour($departure, $hour)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.villeDepart LIKE :departure')
            ->andWhere('a.date > :hour')
            ->setParameter('departure', '%' . $departure . '%')
            ->setParameter('hour', $hour)
            ->getQuery()
            ->getResult();
    }

    public function findByArrivalAndHour($arrival, $hour)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.villeArrive LIKE :arrival')
            ->andWhere('a.date > :hour')
            ->setParameter('arrival', '%' . $arrival . '%')
            ->setParameter('hour', $hour)
            ->getQuery()
            ->getResult();
    }

    public function findByDepartureArrivalAndHour($departure, $arrival, $hour)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.villeDepart LIKE :departure')
            ->andWhere('a.villeArrive LIKE :arrival')
            ->andWhere('a.date > :hour')
            ->setParameters(['departure' => '%' . $departure . '%', 'arrival' => '%' . $arrival . '%', 'hour' => $hour])
            ->getQuery()
            ->getResult();
    }

    public function findByRandomAnnonceExceptUser(User $user)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->join('a.conducteur', 'c')
            ->where('c != :user')
            ->setParameter('user', $user);

        return $queryBuilder->getQuery()->getResult();
    }


}
