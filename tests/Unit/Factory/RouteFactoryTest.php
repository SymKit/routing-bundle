<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Tests\Unit\Factory;

use PHPUnit\Framework\TestCase;
use Symkit\RoutingBundle\Entity\Route;
use Symkit\RoutingBundle\Factory\RouteFactory;

final class RouteFactoryTest extends TestCase
{
    public function testCreateFromEntity(): void
    {
        $entity = new Route();
        $entity->setPath('/blog/{slug}');
        $entity->setController('App\Controller\BlogController::show');

        $factory = new RouteFactory();
        $route = $factory->createFromEntity($entity);

        self::assertSame('/blog/{slug}', $route->getPath());
        self::assertSame('App\Controller\BlogController::show', $route->getDefault('_controller'));
    }
}
