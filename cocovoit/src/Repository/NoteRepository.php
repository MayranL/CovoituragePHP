<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function findAllByConducteurId($conducteurId): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.conducteur = :conducteurId')
            ->setParameter('conducteurId', $conducteurId)
            ->getQuery()
            ->getResult();
    }

    public function findAllByAuteur($passengerId,$annonce,$conducteurId): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.auteur = :auteur')
            ->andWhere('n.annonce = :annonce')
            ->andWhere('n.conducteur = :conducteurId')
            ->setParameter('conducteurId', $conducteurId)
            ->setParameter('auteur', $passengerId)
            ->setParameter('annonce', $annonce)
            ->getQuery()
            ->getResult();
    }


    // Ajoutez ici vos méthodes personnalisées pour le repository de Note
}
