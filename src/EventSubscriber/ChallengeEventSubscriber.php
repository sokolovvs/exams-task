<?php

namespace App\EventSubscriber;

use App\Event\ChallengeCreatedEvent;
use App\Event\ChallengeFinishedEvent;
use Prometheus\RegistryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChallengeEventSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private RegistryInterface $metrcisRegistry;

    public function __construct(LoggerInterface $logger, RegistryInterface $metrcisRegistry)
    {
        $this->logger = $logger;
        $this->metrcisRegistry = $metrcisRegistry;
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
        $this->incEvent('challenge_created');
    }

    public function onChallengeFinished(ChallengeFinishedEvent $event): void
    {
        $this->logger->debug('Try to handle event {event}', ['event' => $event::class, 'id' => $event->challengeId]);
        $this->incEvent('challenge_finished');
    }

    private function incEvent(string $eventName): void
    {
        $this->metrcisRegistry->getOrRegisterCounter('', $eventName, '')->inc();
    }
}
