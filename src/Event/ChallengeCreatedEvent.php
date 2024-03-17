<?php

namespace App\Event;

use Ramsey\Uuid\UuidInterface;

final class ChallengeCreatedEvent
{
    public readonly UuidInterface $challengeId;

    public function __construct(UuidInterface $challengeId)
    {
        $this->challengeId = $challengeId;
    }
}
