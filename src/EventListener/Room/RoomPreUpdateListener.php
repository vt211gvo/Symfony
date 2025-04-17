<?php

namespace App\EventListener\Room;

use App\Entity\Room;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class RoomPreUpdateListener
{
    /**
     * @param PreUpdateEventArgs $args
     * @return void
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $room = $args->getObject();

        if (!$room instanceof Room) {
            return;
        }

        $newPrice = $args->getNewValue('price');

        $roundedPrice = floor($newPrice / 10) * 10 - 1;

        $room->setPrice($newPrice);

        $args->setNewValue('price', $roundedPrice);
    }
}
