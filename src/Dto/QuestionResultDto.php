<?php

namespace App\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(required: ['id', 'content', 'options', 'isCorrectAnswered'])]
final class QuestionResultDto
{
    #[OA\Property(title: 'question ID', format: 'uuid')]
    public readonly string $id;
    public readonly string $content;

    /**
     * @var OptionDto[]
     */
    public readonly array $options;

    public readonly bool $isCorrectAnswered;

    public function __construct(QuestionDto $question, bool $isCorrectAnswered)
    {
        $this->id = $question->id;
        $this->content = $question->content;
        $this->options = $question->options;
        $this->isCorrectAnswered = $isCorrectAnswered;
    }
}