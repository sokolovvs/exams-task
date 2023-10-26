<?php

namespace App\Dto;

use App\Entity\Challenge;

final class ChallengeDto
{
    public readonly string $challengeId;
    public readonly string $examId;
    public readonly string $title;

    /**
     * @var QuestionDto[]
     */
    public readonly array $questions;

    private function __construct(string $challengeId, string $examId, string $title, QuestionDto ...$questions)
    {
        $this->challengeId = $challengeId;
        $this->examId = $examId;
        $this->title = $title;
        $this->questions = $questions;
    }

    public static function fromEntity(Challenge $challenge): self
    {
        $exam = $challenge->getExam();
        $questions = [];
        foreach ($exam->getQuestions() as $question) {
            $questions[] = QuestionDto::fromEntity($question);
        }

        return new self($challenge->getId()->toString(), $exam->getId()->toString(), $exam->getTitle(), ...$questions);
    }
}