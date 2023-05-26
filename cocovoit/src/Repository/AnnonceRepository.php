<?php


namespace App\Repository;

use App\Entity\Annonce;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    public function findExpiredByUser($userId): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.reservations', 'r', Join::WITH, 'r.passager = :userId')
            ->andWhere('a.date < :currentDate')
            ->andWhere('r.passager = :userId')
            ->orderBy('a.date', 'DESC')
            ->setParameter('currentDate', new \DateTime())
            ->setParameter('userId', $userId)
            ->getQuery();

        return $qb->getResult();
    }

    public function findallNotOld(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('not a.date < :currentDate')
            ->andWhere('a.nbplace > 0')
            ->orderBy('a.date', 'ASC')
            ->setParameter('currentDate', new \DateTime())
            ->getQuery();

        return $qb->getResult();
    }

    public function findByDeparture($departure)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.villeDepart LIKE :departure')
            ->andWhere('not a.date < :currentDate')
            ->andWhere('a.nbplace > 0')
            ->orderBy('a.date', 'ASC')
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
            ->andWhere('a.nbplace > 0')
            ->orderBy('a.date', 'ASC')
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
            ->andWhere('a.nbplace > 0')
            ->orderBy('a.date', 'ASC')
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
            ->andWhere('a.nbplace > 0')
            ->orderBy('a.date', 'ASC')
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
            ->andWhere('a.nbplace > 0')
            ->orderBy('a.date', 'ASC')
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
            ->andWhere('a.nbplace > 0')
            ->andWhere('a.date > :hour')
            ->orderBy('a.date', 'ASC')
            ->setParameters(['departure' => '%' . $departure . '%', 'arrival' => '%' . $arrival . '%', 'hour' => $hour])
            ->getQuery()
            ->getResult();
    }

    public function findByRandomAnnonceExceptUser(User $user)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->join('a.conducteur', 'c')
            ->where('c != :user')
            ->orderBy('a.date', 'DESC')
            ->setParameter('user', $user);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAll()
    {
        return $this->findBy(array(), array('date' => 'ASC'));
    }
}
