<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

#[ORM\Entity()]
#[ORM\Table('questions')]
class Question
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(length: 255)]
    private string $content;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exam $exam = null;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Option::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $options;

    public function __construct(string $content, Option ...$options)
    {
        Assert::minCount($options, 2, 'Question must contain 2+ options');
        $this->id = Uuid::uuid4();
        $this->content = $content;
        $this->options = new ArrayCollection();
        foreach ($options as $option) {
            $option->setQuestion($this);
            $this->options->add($option);
        }
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function setExam(Exam $exam): static
    {
        Assert::null($this->exam, "Question{$this->id->toString()} already related exam");
        $this->exam = $exam;

        return $this;
    }
}
