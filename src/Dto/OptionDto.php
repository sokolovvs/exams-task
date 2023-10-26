<?php

namespace App\Dto;

use App\Entity\Option;

final class OptionDto
{
    public readonly string $id;
    public readonly  string $content;

    private function __construct(string $id, string $content)
    {
        $this->id = $id;
        $this->content = $content;
    }

    public static function fromEntity(Option $option): self
    {
        return new self($option->getId(), $option->getContent());
    }
}