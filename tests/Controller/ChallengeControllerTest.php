<?php

namespace App\Tests\Controller;

use App\DataFixtures\ExamFixture;
use App\Service\ExamineeProviderInterface;
use App\Service\StubExamineeProvider;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChallengeControllerTest extends WebTestCase
{
    private TestContainer $testContainer;

    private EntityManagerInterface $em;
    private KernelBrowser $client;
    private Connection $connection;

    private ExamineeProviderInterface|StubExamineeProvider $examineeProvider;

    protected function setUp(): void
    {

        $this->client = self::createClient();
        $this->testContainer = self::$kernel->getContainer()->get('test.service_container');
        $this->em = $this->testContainer->get(EntityManagerInterface::class);
        $this->connection = $this->em->getConnection();
        $this->examineeProvider = $this->testContainer->get(ExamineeProviderInterface::class);
        $this->examineeProvider->setExamineeId('3486f8a2-3996-4d5b-9d60-b2b89bbf28f7');
    }

    public function testStartChallengeOk(): void
    {
        $location = $this->startChallenge();
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertStringStartsWith('/challenges/', $location);
    }

    public function testCanNotStartChallenge(): void
    {
        $examId = Uuid::uuid4()->toString();
        $this->client->request(Request::METHOD_POST, "/exams/$examId/challenges");
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertNull($response->getHeader('Location'));
    }

    public function testCanNotStartChallengeAsUnknownExaminee(): void
    {
        $this->examineeProvider->setExamineeId(null);
        $location = $this->startChallenge();
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertNull($location);
    }

    public function testGetChallengeOk(): void
    {
        $location = $this->startChallenge();
        $this->client->request(Request::METHOD_GET, $location);
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertChallenge(json_decode($response->getContent(), true));
    }

    public function testGetChallengeNotFound(): void
    {
        $challengeId = Uuid::uuid4()->toString();
        $this->client->request(Request::METHOD_GET, "/challenges/$challengeId");
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetChallengeNotFoundIfAnotherExaminee(): void
    {
        $this->examineeProvider->setExamineeId(Uuid::uuid4()->toString());
        $challengeId = Uuid::uuid4()->toString();
        $this->client->request(Request::METHOD_GET, "/challenges/$challengeId");
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testFinishChallengeSuccess(): void
    {
        $location = $this->startChallenge();
        $challengeId = explode('/', $location)[2];
        $answers = $this->fetchChallengeResponses($challengeId, true);
        $this->client->request(Request::METHOD_PUT, "$location/finish", [], [], [], json_encode($answers));
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testCantFinishUnknownChallenge(): void
    {
        $location = $this->startChallenge();
        $challengeId = Uuid::uuid4()->toString();
        $answers = $this->fetchChallengeResponses($challengeId, true);
        $this->client->request(Request::METHOD_PUT, "/challenges/$challengeId/finish", [], [], [], json_encode($answers));
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCantFinishChallengeIfChallengeOrOptionDontRelateWithChallenge(): void
    {
        $location = $this->startChallenge();
        $anotherLocation = $this->startChallenge();
        $anotherChallengeId = explode('/', $anotherLocation)[2];
        $answers = $this->fetchChallengeResponses($anotherChallengeId, true);
        $this->client->request(Request::METHOD_PUT, "$location/finish", [], [], [], json_encode($answers));
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testCantFinishChallengeIfOptionDuplicated(): void
    {
        $location = $this->startChallenge();
        $challengeId = explode('/', $location)[2];
        $answers = $this->fetchChallengeResponses($challengeId, true);
        $answers[] = $answers[0];
        $this->client->request(Request::METHOD_PUT, "$location/finish", [], [], [], json_encode($answers));
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testCantFinishAlreadyFinishedChallenge(): void
    {
        $location = $this->startChallenge();
        $challengeId = explode('/', $location)[2];
        $answers = $this->fetchChallengeResponses($challengeId, true);
        $this->client->request(Request::METHOD_PUT, "$location/finish", [], [], [], json_encode($answers));
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->client->request(Request::METHOD_PUT, "$location/finish", [], [], [], json_encode($answers));
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testChallengeResultsSuccess(): void
    {
        $location = $this->startChallenge();
        $challengeId = explode('/', $location)[2];
        $correctAnswers = $this->fetchChallengeResponses($challengeId, true);
        $this->client->request(Request::METHOD_PUT, "$location/finish", [], [], [], json_encode($correctAnswers));
        $this->client->request(Request::METHOD_GET, "$location/results");
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertChallengeResults(json_decode($response->getContent(), true));
    }

    public function testChallengeResultsNotFound(): void
    {
        $location = $this->startChallenge();
        $challengeId = explode('/', $location)[2];
        $randomUuid = Uuid::uuid4()->toString();
        $correctAnswers = $this->fetchChallengeResponses($challengeId, true);
        $this->client->request(Request::METHOD_PUT, "$location/finish", [], [], [], json_encode($correctAnswers));
        $this->client->request(Request::METHOD_GET, "/challenges/$randomUuid/results");
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testChallengeResultsAreNotReady(): void
    {
        $location = $this->startChallenge();
        $this->client->request(Request::METHOD_GET, "$location/results");
        $response = $this->client->getInternalResponse();
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    private function addExam(): string
    {
        $this->em->persist($exam = ExamFixture::exam());
        $this->em->flush();

        return $exam->getId()->toString();
    }

    private function startChallenge(): ?string
    {
        $examId = $this->addExam();
        $this->client->request(Request::METHOD_POST, "/exams/$examId/challenges");
        $response = $this->client->getInternalResponse();

        return $response->getHeader('Location');
    }

    private static function assertChallenge(array $decodedResponse): void
    {
        self::assertIsValidUuid($decodedResponse['challengeId']);
        self::assertIsValidUuid($decodedResponse['examId']);
        self::assertEquals('Math test', $decodedResponse['title']);
        self::assertCount(10, $decodedResponse['questions']);
        foreach ($decodedResponse['questions'] as $question) {
            self::assertIsValidUuid($question['id']);
            self::assertStringEndsWith('=', $question['content']);
            self::assertIsArray($question['options']);
            foreach ($question['options'] as $option) {
                self::assertIsValidUuid($option['id']);
                self::assertIsString($option['content']);
            }
        }
    }

    private static function assertIsValidUuid($uuid, string $message = '')
    {
        $pattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
        self::assertMatchesRegularExpression($pattern, $uuid, $message);
    }

    private function fetchChallengeResponses(string $challengeId, bool $isCorrect, ?int $limit = null): array
    {
        $limit = $limit ? "LIMIT $limit" : '';
        return $this->connection->fetchAllAssociative(
            <<<SQL

SELECT o.id AS "optionId", o.question_id AS "questionId"
FROM challenges ch
         JOIN exams e ON e.id = ch.exam_id
         JOIN questions q ON q.exam_id = e.id
         JOIN options o ON o.question_id = q.id
WHERE o.is_correct = :is_correct
  AND ch.id = :challenge_id
$limit
;

SQL,
            [
                'challenge_id' => $challengeId,
                'is_correct' => $isCorrect,
            ],
            [
                'challenge_id' => ParameterType::STRING,
                'is_correct' => ParameterType::BOOLEAN,
            ]
        );
    }

    private function assertChallengeResults(array $decodedResponse): void
    {
        self::assertIsValidUuid($decodedResponse['id']);
        foreach ($decodedResponse['questions'] as $question) {
            self::assertIsValidUuid($question['id']);
            self::assertStringEndsWith('=', $question['content']);
            self::assertIsArray($question['options']);
            self::assertIsBool($question['isCorrectAnswered']);
            foreach ($question['options'] as $option) {
                self::assertIsValidUuid($option['id']);
                self::assertIsString($option['content']);
            }
        }
    }
}