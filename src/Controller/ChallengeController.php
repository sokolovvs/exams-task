<?php

namespace App\Controller;

use App\Dto\AnswerDto;
use App\Dto\ChallengeDto;
use App\Dto\ChallengeResultDto;
use App\Service\ChallengeServiceInterface;
use App\Service\ExamineeProviderInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

class ChallengeController
{
    #[Route(path: '/api/v1/exams/{examId}/challenges', requirements: ['examId' => Requirement::UUID_V4], methods: ['POST'])]
    #[OA\Response(
        response: 201,
        description: 'Ok. Challenge started',
        headers: [new OA\Header(null, 'Location', 'Access to the challenge', true, new OA\Schema())],
        content: new OA\JsonContent(
            type: null,
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Unknown challenge'
    )]
    #[OA\Parameter(
        name: 'examId',
        in: 'path',
        schema: new OA\Schema(type: 'string', format: 'uuid')
    )]
    #[OA\Tag(name: 'challenge')]
    #[OA\Tag(name: 'v1')]
    public function startChallenge(
        string $examId,
        ExamineeProviderInterface $examineeProvider,
        ChallengeServiceInterface $challengeService,
        UrlGeneratorInterface $router,
        LoggerInterface $logger
    ): JsonResponse
    {
        try {
            $challengeId = (string)$challengeService->startChallenge($examineeProvider->getExamineeId(), $examId);

            return new JsonResponse(
                null,
                Response::HTTP_CREATED,
                [
                    'Location' => $router->generate('challenge_by_id', ['challengeId' => $challengeId]),
                ]
            );
        } catch (\OutOfBoundsException $notFoundException) {
            $logger->warning('Can not start challenge for unknown exam', ['exception' => $notFoundException, 'examId' => $examId,]);

            return new JsonResponse(['message' => 'Exam not found'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route(
        path: '/api/v1/challenges/{challengeId}',
        name: 'challenge_by_id',
        requirements: ['challengeId' => Requirement::UUID_V4],
        methods: ['GET']
    )]
    #[OA\Response(
        response: 200,
        description: 'Ok',
        content: new OA\JsonContent(ref: new Model(type: ChallengeDto::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Unknown challenge'
    )]
    #[OA\Parameter(
        name: 'challengeId',
        in: 'path',
        schema: new OA\Schema(type: 'string', format: 'uuid')
    )]
    #[OA\Tag(name: 'challenge')]
    #[OA\Tag(name: 'v1')]
    public function challenge(
        string $challengeId,
        ChallengeServiceInterface $challengeService,
        ExamineeProviderInterface $examineeProvider,
        LoggerInterface $logger
    ): JsonResponse
    {
        try {
            return new JsonResponse($challengeService->getChallenge($challengeId, $examineeProvider->getExamineeId()));
        } catch (\OutOfBoundsException $notFoundException) {
            $logger->warning('Unknown challenge', ['exception' => $notFoundException, 'challengeId' => $challengeId,]);

            return new JsonResponse(['message' => 'Challenge not found'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route(path: '/api/v1/challenges/{challengeId}/finish', requirements: ['challengeId' => Requirement::UUID_V4], methods: ['PUT'])]
    #[OA\Response(
        response: 200,
        description: 'Ok',
        content: new OA\JsonContent(type: null)
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: AnswerDto::class))
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid input'
    )]
    #[OA\Response(
        response: 404,
        description: 'Unknown challenge'
    )]
    #[OA\Parameter(
        name: 'challengeId',
        in: 'path',
        schema: new OA\Schema(type: 'string', format: 'uuid')
    )]
    #[OA\Tag(name: 'challenge')]
    #[OA\Tag(name: 'v1')]
    public function finishChallenge(
        string $challengeId,
        ChallengeServiceInterface $challengeService,
        ExamineeProviderInterface $examineeProvider,
        SerializerInterface $serializer,
        Request $request,
        LoggerInterface $logger
    ): JsonResponse
    {
        try {
            $answers = $serializer->deserialize($request->getContent(), sprintf("%s[]", AnswerDto::class), 'json');
            $challengeService->finishChallenge($challengeId, $examineeProvider->getExamineeId(), ...$answers);

            return new JsonResponse();
        } catch (\OutOfBoundsException $notFoundException) {
            $logger->warning('Unknown challenge', ['exception' => $notFoundException, 'challengeId' => $challengeId,]);

            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        } catch (\DomainException $domainException) {
            $logger->error('Can not finish challenge', ['exception' => $domainException, 'challengeId' => $challengeId,]);

            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            $logger->error('Can not finish challenge. Internal error.', ['exception' => $e, 'challengeId' => $challengeId,]);

            return new JsonResponse(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/api/v1/challenges/{challengeId}/results', requirements: ['challengeId' => Requirement::UUID_V4], methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Ok',
        content: new OA\JsonContent(ref: new Model(type: ChallengeResultDto::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Can not see results of unfinished challenge'
    )]
    #[OA\Response(
        response: 404,
        description: 'Unknown challenge'
    )]
    #[OA\Parameter(
        name: 'challengeId',
        in: 'path',
        schema: new OA\Schema(type: 'string', format: 'uuid')
    )]
    #[OA\Tag(name: 'challenge')]
    #[OA\Tag(name: 'v1')]
    public function getChallengeResults(
        string $challengeId,
        ChallengeServiceInterface $challengeService,
        ExamineeProviderInterface $examineeProvider,
        LoggerInterface $logger
    ): JsonResponse
    {
        try {
            return new JsonResponse($challengeService->getChallengeResults($challengeId, $examineeProvider->getExamineeId()));
        } catch (\OutOfBoundsException $notFoundException) {
            $logger->warning('Unknown challenge', ['exception' => $notFoundException, 'challengeId' => $challengeId,]);

            return new JsonResponse(['message' => 'Challenge not found'], Response::HTTP_NOT_FOUND);
        } catch (\DomainException $domainException) {
            $logger->warning('Can not see results of unfinished challenge', ['exception' => $domainException, 'challengeId' => $challengeId,]);

            return new JsonResponse(['message' => $domainException->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}