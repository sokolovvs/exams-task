<?php

namespace App\Repository;

use App\Entity\Challenge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Challenge>
 *
 * @method Challenge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Challenge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Challenge[]    findAll()
 * @method Challenge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChallengeRepository extends ServiceEntityRepository implements ChallengeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Challenge::class);
    }

    public function save(Challenge $challenge): void
    {
        $this->_em->persist($challenge);
        $this->_em->flush();
    }

    public function getById(string $id): Challenge
    {
        $qb = $this->createQueryBuilder('ch');
        $qb->select('ch', 'e', 'q', 'o')
            ->innerJoin('ch.exam', 'e')
            ->innerJoin('e.questions', 'q')
            ->innerJoin('q.options', 'o')
            ->andWhere('ch.id = :id')
            ->setParameter('id', $id);

        $challenge = $qb->getQuery()->getOneOrNullResult();

        if ($challenge === null) {
            throw new \OutOfBoundsException("Challenge#$id not found");
        }

        return $challenge;
    }
}
