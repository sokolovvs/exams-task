<?php

namespace App\EventSubscriber;

use App\Event\ChallengeCreatedEvent;
use App\Event\ChallengeFinishedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChallengeEventSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ChallengeCreatedEvent::class => ['onChallengeCreated'],
            ChallengeFinishedEvent::class => ['onChallengeFinished'],
        ];
    }

    public function onChallengeCreated(ChallengeCreatedEvent $event): void
    {
        $this->logger->debug('Try to handle event {event}', ['event' => $event::class, 'id' => $event->challengeId]);
    }

    public function onChallengeFinished(ChallengeFinishedEvent $event): void
    {
        $this->logger->debug('Try to handle event {event}', ['event' => $event::class, 'id' => $event->challengeId]);
    }
}
