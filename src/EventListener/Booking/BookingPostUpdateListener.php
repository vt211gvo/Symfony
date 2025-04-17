<?php

namespace App\EventListener\Booking;

use App\Entity\Booking;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Psr\Log\LoggerInterface;

class BookingPostUpdateListener
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param PostUpdateEventArgs $args
     * @return void
     */
    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $booking = $args->getObject();

        if (!$booking instanceof Booking) {
            return;
        }

        $entityManager = $args->getObjectManager();

        $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($booking);

        $message = sprintf(
            "Booking #%d was updated. Changes: %s",
            $booking->getId(),
            json_encode($changeSet)
        );

        $this->logger->info($message);
    }
}
