<?php

namespace App\Repository;

use App\Entity\Answer;

interface AnswerRepositoryInterface
{
    public function save(Answer ...$answers): void;
}