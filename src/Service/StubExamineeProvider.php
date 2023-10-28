<?php

namespace App\Service;

use Ramsey\Uuid\Uuid;

class StubExamineeProvider implements ExamineeProviderInterface
{
    private ?string $examineeId;

    public function __construct(?string $examineeId = '3486f8a2-3996-4d5b-9d60-b2b89bbf28f7')
    {
        $this->examineeId = $examineeId;
    }

    public function setExamineeId(?string $examineeId): void
    {
        $this->examineeId = $examineeId;
    }

    public function getExamineeId(): string
    {
        if ($this->examineeId === null) {
            throw new \OutOfBoundsException('Unknown examinee');
        }

        return $this->examineeId;
    }
}