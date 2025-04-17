<?php

declare(strict_types=1);

namespace App\Extension\Booking;

use App\Extension\UserRelationExtension;
use Doctrine\ORM\QueryBuilder;

class BookingUserExtension extends UserRelationExtension
{
    /**
     * @return string
     */
    public function getResourceClass(): string
    {
        return 'App\Entity\Booking';
    }

    /**
     * Creates a query to filter the current user's bookings
     *
     * @param QueryBuilder $queryBuilder
     * @return void
     */
    public function buildQuery(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[self::FIRST_ELEMENT_ARRAY];
        $queryBuilder
            ->andWhere($rootAlias . '.user = :user')
            ->setParameter('user', $this->security->getUser());
    }
}
