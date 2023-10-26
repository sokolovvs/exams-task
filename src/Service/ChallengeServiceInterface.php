<?php

namespace App\Service;

use App\Dto\AnswerDto;
use App\Dto\ChallengeDto;
use App\Dto\ChallengeResultDto;
use Ramsey\Uuid\UuidInterface;

interface ChallengeServiceInterface
{
    /**
     * @throws \OutOfBoundsException when exam / examinee is unknown
     */
    public function startChallenge(string $examineeId, string $examId): UuidInterface;

    /**
     * @throws \OutOfBoundsException when challenge not found
     */
    public function getChallenge(string $challengeId, string $examineeId): ChallengeDto;

    /**
     * @throws \OutOfBoundsException when challenge not found
     * @throws \DomainException when challenge is already finished OR input data invalid
     */
    public function finishChallenge(string $challengeId, string $examineeId, AnswerDto ...$answers): void;

    /**
     * @throws \OutOfBoundsException when challenge not found
     */
    public function getChallengeResults(string $challengeId, string $examineeId): ChallengeResultDto;
}