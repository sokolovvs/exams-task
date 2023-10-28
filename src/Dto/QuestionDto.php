<?php

namespace App\Dto;

use App\Entity\Question;
use OpenApi\Attributes as OA;

#[OA\Schema(required: ['id', 'content', 'options'])]
final class QuestionDto
{
    #[OA\Property(title: 'question ID', format: 'uuid')]
    public readonly string $id;
    public readonly string $content;

    /**
     * @var OptionDto[]
     */
    public readonly array $options;

    private function __construct(string $id, string $content, OptionDto ...$options)
    {
        $this->id = $id;
        $this->content = $content;
        $this->options = $options;
    }

    public static function fromEntity(Question $question, bool $shuffle = false): self
    {
        $optionsDto = [];
        $options = $question->getOptions();
        if ($shuffle) {
            $options = $options->toArray();
            shuffle($options);
        }
        foreach ($options as $option) {
            $optionsDto[] = OptionDto::fromEntity($option);
        }
        return new self($question->getId(), $question->getContent(), ...$optionsDto);
    }
}