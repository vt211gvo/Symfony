<?php

declare(strict_types=1);

namespace App\Extension\Room;

use App\Extension\AbstractCurrentUserExtension;
use Doctrine\ORM\QueryBuilder;

class RoomStatusExtension extends AbstractCurrentUserExtension
{
    /**
     * @return string
     */
    public function getResourceClass(): string
    {
        return 'App\Entity\Room';
    }

    /**
     * Creates a query to filter rooms by status
     *
     * @param QueryBuilder $queryBuilder
     * @return void
     */
    public function buildQuery(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[self::FIRST_ELEMENT_ARRAY];
        $status = 'available';

        $queryBuilder
            ->andWhere($rootAlias . '.status = :status')
            ->setParameter('status', $status);
    }
}
