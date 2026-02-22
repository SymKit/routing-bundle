<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Resource;

use Symfony\Component\Config\Resource\ResourceInterface;

final readonly class RoutingDatabaseResource implements ResourceInterface
{
    public function __construct(
        private readonly int $count,
        private readonly int $lastUpdate,
    ) {
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getLastUpdate(): int
    {
        return $this->lastUpdate;
    }

    public function __toString(): string
    {
        return 'routing_database_resource';
    }
}
