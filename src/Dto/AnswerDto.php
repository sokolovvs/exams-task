<?php

namespace App\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(required: ['questionId', 'optionId'])]
final class AnswerDto
{
    #[OA\Property(format: 'uuid')]
    public readonly string $questionId;
    #[OA\Property(format: 'uuid')]
    public readonly string $optionId;

    public function __construct(string $questionId, string $optionId)
    {
        $this->questionId = $questionId;
        $this->optionId = $optionId;
    }
}