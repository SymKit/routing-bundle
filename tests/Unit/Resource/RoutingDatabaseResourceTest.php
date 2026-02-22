<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Tests\Unit\Resource;

use PHPUnit\Framework\TestCase;
use Symkit\RoutingBundle\Resource\RoutingDatabaseResource;

final class RoutingDatabaseResourceTest extends TestCase
{
    public function testGettersAndToString(): void
    {
        $resource = new RoutingDatabaseResource(10, 12345);

        self::assertSame(10, $resource->getCount());
        self::assertSame(12345, $resource->getLastUpdate());
        self::assertSame('routing_database_resource', (string) $resource);
    }
}
