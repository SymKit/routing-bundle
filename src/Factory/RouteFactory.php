<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Factory;

use Symfony\Component\Routing\Route;
use Symkit\RoutingBundle\Contract\RouteEntityInterface;

final readonly class RouteFactory
{
    public function createFromEntity(RouteEntityInterface $entity): Route
    {
        $defaults = $entity->getDefaults();
        $defaults['_controller'] = $entity->getController() ?? '';

        return new Route(
            $entity->getPath() ?? '',
            $defaults,
            $entity->getRequirements(),
            $entity->getOptions(),
            $entity->getHost() ?? '',
            $entity->getSchemes(),
            $entity->getMethods(),
            $entity->getCondition() ?? '',
        );
    }
}
