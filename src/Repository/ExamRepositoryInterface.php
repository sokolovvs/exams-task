<?php

namespace App\Repository;

use App\Entity\Exam;

interface ExamRepositoryInterface
{
    /**
     * @throws \OutOfBoundsException when exam not found
     */
    public function getById(string $examId): Exam;

    public function save(Exam $exam): void;
}