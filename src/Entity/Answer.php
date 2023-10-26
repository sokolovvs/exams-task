<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: AnswerRepository::class)]
#[ORM\Table('answers')]
class Answer
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\ManyToOne(inversedBy: 'answers')]
    private Challenge $challenge;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Question $question;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Option $option;

    public function __construct(Challenge $challenge, Question $question, Option $option)
    {
        $this->id = Uuid::uuid4();
        $this->challenge = $challenge;
        $this->question = $question;
        $this->option = $option;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function getOption(): Option
    {
        return $this->option;
    }
}
