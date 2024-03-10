<?php

namespace App\Service;

use App\Dto\ChallengeResultDto;
use App\Entity\Challenge;

interface ResultCalculatorInterface
{
    public function calculate(Challenge $challenge): ChallengeResultDto;
}
