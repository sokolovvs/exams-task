<?php

namespace App\Dto;
use OpenApi\Attributes as OA;

#[OA\Schema(required: ['id', 'questions',])]
final class ChallengeResultDto
{
    #[OA\Property(title: 'Challenge ID', format: 'uuid')]
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