<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

#[ORM\Entity()]
#[ORM\Table('options')]
class Option
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(length: 255)]
    private string $content;

    #[ORM\ManyToOne(inversedBy: 'options')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    #[ORM\Column]
    private bool $isCorrect;

    public function __construct(string $content, bool $isCorrect)
    {
        $this->id = Uuid::uuid4();
        $this->content = $content;
        $this->isCorrect = $isCorrect;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isCorrect(): bool
    {
        return $this->isCorrect;
    }

    public function setQuestion(Question $question): static
    {
        Assert::null($this->question, "Option{$this->id->toString()} already related exam");
        $this->question = $question;

        return $this;
    }
}
