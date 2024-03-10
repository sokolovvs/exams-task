<?php

namespace App\Repository;

use App\Entity\Exam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Exam>
 *
 * @method Exam|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exam|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exam[]    findAll()
 * @method Exam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamRepository extends ServiceEntityRepository implements ExamRepositoryInterface
{
    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Exam::class);
        $this->logger = $logger;
    }

    public function getById(string $examId): Exam
    {
        $exam = $this->find($examId);
        if (null === $exam) {
            throw new \OutOfBoundsException("Exam#$examId not found");
        }

        return $exam;
    }

    public function save(Exam $exam): void
    {
        $this->_em->beginTransaction();
        try {
            $this->_em->persist($exam);
            $this->_em->flush();
            $this->_em->commit();
        } catch (\Throwable $e) {
            $this->logger->error('Exam saving failed', ['exception' => $e]);
            $this->_em->rollback();
            throw $e;
        }
    }
}
