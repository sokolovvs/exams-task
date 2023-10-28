<?php

namespace App\Dto;

use App\Entity\Challenge;
use OpenApi\Attributes as OA;

#[OA\Schema(required: ['challengeId', 'examId', 'title', 'questions'])]
final class ChallengeDto
{
    #[OA\Property(format: 'uuid')]
    public readonly string $challengeId;
    #[OA\Property(format: 'uuid')]
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

    public static function fromEntity(Challenge $challenge, bool $shuffle = false): self
    {
        $questionsDto = [];
        $exam = $challenge->getExam();
        $questions = $exam->getQuestions();
        if ($shuffle) {
            $questions = $questions->toArray();
            shuffle($questions);
        }
        foreach ($questions as $question) {
            $questionsDto[] = QuestionDto::fromEntity($question, $shuffle);
        }

        return new self($challenge->getId()->toString(), $exam->getId()->toString(), $exam->getTitle(), ...$questionsDto);
    }
}