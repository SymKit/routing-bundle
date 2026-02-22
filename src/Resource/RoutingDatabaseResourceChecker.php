<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Resource;

use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\Config\ResourceCheckerInterface;
use Symkit\RoutingBundle\Repository\RouteRepository;

final readonly class RoutingDatabaseResourceChecker implements ResourceCheckerInterface
{
    public function __construct(
        private readonly RouteRepository $routeRepository,
    ) {
    }

    public function supports(ResourceInterface $resource): bool
    {
        return $resource instanceof RoutingDatabaseResource;
    }

    /**
     * @param RoutingDatabaseResource $resource
     */
    public function isFresh(ResourceInterface $resource, int $timestamp): bool
    {
        $currentState = $this->routeRepository->getRoutingState();

        return $currentState['count'] === $resource->getCount()
            && $currentState['lastUpdate'] === $resource->getLastUpdate();
    }
}
