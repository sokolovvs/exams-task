<?php

namespace App\Dto;

use App\Entity\Question;

final class QuestionDto
{
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

    public static function fromEntity(Question $question): self
    {
        $options = [];
        foreach ($question->getOptions() as $option) {
            $options[] = OptionDto::fromEntity($option);
        }
        return new self($question->getId(), $question->getContent(), ...$options);
    }
}