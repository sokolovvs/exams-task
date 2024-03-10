<?php

namespace App\Service;

interface ExamineeProviderInterface
{
    /**
     * @throws \OutOfBoundsException when can't resolve an examinee
     */
    public function getExamineeId(): string;
}
