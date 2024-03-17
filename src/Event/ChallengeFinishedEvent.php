<?php

namespace App\Event;

use Ramsey\Uuid\UuidInterface;

final class ChallengeFinishedEvent
{
    public readonly UuidInterface $challengeId;

    public function __construct(UuidInterface $challengeId)
    {
        $this->challengeId = $challengeId;
    }
}
