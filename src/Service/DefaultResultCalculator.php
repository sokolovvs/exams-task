<?php

namespace App\Service;

use App\Dto\ChallengeResultDto;
use App\Dto\QuestionDto;
use App\Dto\QuestionResultDto;
use App\Entity\Challenge;

class DefaultResultCalculator implements ResultCalculatorInterface
{
    public function calculate(Challenge $challenge): ChallengeResultDto
    {
        $answersHash = [];
        foreach ($challenge->getAnswers() as $answer) {
            $answersHash[$answer->getQuestion()->getId()->toString()][] = $answer->getOption()->getId()->toString();
        }
        $results = [];
        $exam = $challenge->getExam();
        foreach ($exam->getQuestions() as $question) {
            $isCorrect = isset($answersHash[$question->getId()->toString()]);
            if ($isCorrect) {
                foreach ($question->getOptions() as $option) {
                    if (in_array($option->getId()->toString(), $answersHash[$question->getId()->toString()])) {
                        $isCorrect = $isCorrect && $option->isCorrect();
                    }
                }
            }
            $results[] = new QuestionResultDto(QuestionDto::fromEntity($question), $isCorrect);
        }

        return new ChallengeResultDto($challenge->getId()->toString(), ...$results);
    }
}
