<?php

namespace App\Entity;

use App\Repository\AnswerRepositoryInterface;
use App\Repository\ChallengeRepository;
use App\Repository\ChallengeRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: ChallengeRepository::class)]
#[ORM\Table('challenges')]
class Challenge
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Exam $exam;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $finishedAt;

    #[ORM\Column(nullable: false)]
    private string $examineeId;

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(mappedBy: 'challenge', targetEntity: Answer::class)]
    private Collection $answers;

    public function __construct(Exam $exam, string $examineeId)
    {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTimeImmutable();
        $this->exam = $exam;
        $this->examineeId = $examineeId;
        $this->answers = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getExam(): Exam
    {
        return $this->exam;
    }

    public function getExamineeId(): string
    {
        return $this->examineeId;
    }

    public function isFinished(): bool
    {
        return $this->finishedAt !== null;
    }

    public function finish(
        ChallengeRepositoryInterface $challengeRepository,
        AnswerRepositoryInterface $answersRepository,
        Answer ...$answers
    ): void
    {
        if ($this->isFinished()) {
            throw new \DomainException("The challenge#{$this->getId()->toString()} is already finished");
        }
        $answersRepository->save(...$answers);
        $this->finishedAt = new \DateTimeImmutable();
        $challengeRepository->save($this);
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }
}
