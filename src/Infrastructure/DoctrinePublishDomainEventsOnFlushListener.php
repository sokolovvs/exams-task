<?php

namespace App\Infrastructure;

use App\Domain\Interfaces\EventEmitterInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

final class DoctrinePublishDomainEventsOnFlushListener
{
    private $dispatcher;

    private $logger;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    ) {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $unitOfWork = $eventArgs->getObjectManager()->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            $this->publishDomainEvent($entity);
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            $this->publishDomainEvent($entity);
        }
    }

    private function publishDomainEvent(object $entity): void
    {
        if (!$entity instanceof EventEmitterInterface) {
            return;
        }

        foreach ($entity->popEvents() as $event) {
            try {
                $this->dispatcher->dispatch($event);
            } catch (\Throwable $e) {
                $this->logger->error('Event dispatching failed', ['exception' => $e, 'eventName' => get_class($event)]);
            }
        }
    }
}
