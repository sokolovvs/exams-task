<?php

namespace App\Dto;

final class AnswerDto
{
    public readonly string $questionId;
    public readonly string $optionId;

    public function __construct(string $questionId, string $optionId)
    {
        $this->questionId = $questionId;
        $this->optionId = $optionId;
    }
}