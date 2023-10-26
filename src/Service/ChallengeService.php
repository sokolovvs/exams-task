<?php

namespace App\Service;

use App\Dto\AnswerDto;
use App\Dto\ChallengeDto;
use App\Dto\ChallengeResultDto;
use App\Entity\Answer;
use App\Entity\Challenge;
use App\Repository\AnswerRepositoryInterface;
use App\Repository\ChallengeRepositoryInterface;
use App\Repository\ExamRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\UuidInterface;

class ChallengeService implements ChallengeServiceInterface
{
    private ExamRepositoryInterface $exams;
    private ChallengeRepositoryInterface $challenges;
    private AnswerRepositoryInterface $answerRepository;
    private LoggerInterface $logger;
    private EntityManagerInterface $em;
    private ResultCalculatorInterface $resultCalculator;

    public function __construct(
        ExamRepositoryInterface $exams,
        ChallengeRepositoryInterface $challenges,
        AnswerRepositoryInterface $answerRepository,
        LoggerInterface $logger,
        EntityManagerInterface $em,
        ResultCalculatorInterface $resultCalculator
    )
    {
        $this->exams = $exams;
        $this->challenges = $challenges;
        $this->answerRepository = $answerRepository;
        $this->logger = $logger;
        $this->em = $em;
        $this->resultCalculator = $resultCalculator;
    }

    public function startChallenge(string $examineeId, string $examId): UuidInterface
    {
        $challenge = new Challenge($this->exams->getById($examId), $examineeId);
        $this->challenges->save($challenge);

        return $challenge->getId();
    }

    public function getChallenge(string $challengeId, string $examineeId): ChallengeDto
    {
        return ChallengeDto::fromEntity($this->getChallengeForExaminee($challengeId, $examineeId));
    }

    public function finishChallenge(string $challengeId, string $examineeId, AnswerDto ...$answers): void
    {
        $challenge = $this->getChallengeForExaminee($challengeId, $examineeId);
        $this->writeChallengeResult($challenge, ...$this->answersDtoToAnswersEntities($challenge, ...$answers));
    }

    public function getChallengeResults(string $challengeId, string $examineeId): ChallengeResultDto
    {
        $challenge = $this->getChallengeForExaminee($challengeId, $examineeId);
        if (!$challenge->isFinished()) {
            throw new \DomainException("Challenge#$challengeId no finished");
        }

        return $this->resultCalculator->calculate($challenge);
    }

    private function writeChallengeResult(Challenge $challenge, Answer ...$answers): void
    {
        $this->em->beginTransaction();
        try {
            $challenge->finish($this->challenges, $this->answerRepository, ...$answers);
            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollback();
            $this->logger->error("Challenge finishing failed", ['exception' => $e, 'challengeId' => $challenge->getId()]);
            throw $e;
        }
    }

    /**
     * @return Answer[]
     */
    private function answersDtoToAnswersEntities(Challenge $challenge, AnswerDto ...$answers): array
    {
        $exam = $challenge->getExam();
        $questionsOptionsHash = [];
        $questionsHash = [];
        foreach ($exam->getQuestions() as $question) {
            $questionsHash[$question->getId()->toString()] = $question;
            foreach ($question->getOptions() as $option) {
                $questionsOptionsHash[$question->getId()->toString()][$option->getId()->toString()] = $option;
            }
        }
        $filteredAnswers = [];
        foreach ($answers as $answer) {
            if (!isset($questionsOptionsHash[$answer->questionId][$answer->optionId])) {
                throw new \DomainException("Wrong answer for question#{$answer->questionId} and option#{$answer->optionId}");
            }
            $filteredAnswers[] = new Answer(
                $challenge,
                $questionsHash[$answer->questionId],
                $questionsOptionsHash[$answer->questionId][$answer->optionId]
            );
        }

        return $filteredAnswers;
    }

    private function getChallengeForExaminee(string $challengeId, string $examineeId): Challenge
    {
        $challenge = $this->challenges->getById($challengeId);
        if ($examineeId !== $challenge->getExamineeId()) {
            throw new \OutOfBoundsException("Challenge#$challengeId not found");
        }

        return $challenge;
    }
}