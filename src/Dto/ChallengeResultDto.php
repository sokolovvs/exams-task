<?php

namespace App\Dto;

final class ChallengeResultDto
{
    public readonly string $id;

    /**
     * @var QuestionResultDto[]
     */
    public readonly array $questions;

    public function __construct(string $id, QuestionResultDto ...$questions)
    {
        $this->id = $id;
        $this->questions = $questions;
    }
}