<?php

namespace App\Dto;

use App\Entity\Option;
use OpenApi\Attributes as OA;

#[OA\Schema(required: ['id', 'content'])]
final class OptionDto
{
    #[OA\Property(title: 'option ID', format: 'uuid')]
    public readonly string $id;
    public readonly string $content;

    private function __construct(string $id, string $content)
    {
        $this->id = $id;
        $this->content = $content;
    }

    public static function fromEntity(Option $option): self
    {
        return new self($option->getId()->toString(), $option->getContent());
    }
}
