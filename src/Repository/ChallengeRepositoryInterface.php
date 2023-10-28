<?php

namespace App\Repository;

use App\Entity\Challenge;

interface ChallengeRepositoryInterface
{
    /**
     * @throws \OutOfBoundsException when challenge does not exist
     */
    public function getById(string $id): Challenge;

    public function save(Challenge $challenge): void;
}