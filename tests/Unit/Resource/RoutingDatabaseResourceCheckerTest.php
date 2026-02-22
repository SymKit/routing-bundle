<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Tests\Unit\Resource;

use PHPUnit\Framework\TestCase;
use Symkit\RoutingBundle\Repository\RouteRepository;
use Symkit\RoutingBundle\Resource\RoutingDatabaseResource;
use Symkit\RoutingBundle\Resource\RoutingDatabaseResourceChecker;

final class RoutingDatabaseResourceCheckerTest extends TestCase
{
    public function testSupportsOnlyRoutingDatabaseResource(): void
    {
        $repository = $this->createMock(RouteRepository::class);
        $checker = new RoutingDatabaseResourceChecker($repository);

        self::assertTrue($checker->supports(new RoutingDatabaseResource(5, 12345)));
    }

    public function testIsFreshWhenStateMatches(): void
    {
        $repository = $this->createMock(RouteRepository::class);
        $repository->method('getRoutingState')->willReturn(['count' => 3, 'lastUpdate' => 100]);

        $checker = new RoutingDatabaseResourceChecker($repository);
        $resource = new RoutingDatabaseResource(3, 100);

        self::assertTrue($checker->isFresh($resource, time()));
    }

    public function testIsNotFreshWhenCountDiffers(): void
    {
        $repository = $this->createMock(RouteRepository::class);
        $repository->method('getRoutingState')->willReturn(['count' => 4, 'lastUpdate' => 100]);

        $checker = new RoutingDatabaseResourceChecker($repository);
        $resource = new RoutingDatabaseResource(3, 100);

        self::assertFalse($checker->isFresh($resource, time()));
    }
}
