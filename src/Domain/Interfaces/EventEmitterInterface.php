<?php

namespace App\Domain\Interfaces;

interface EventEmitterInterface
{
    /**
     * @return array<object>
     */
    public function popEvents(): array;
}
