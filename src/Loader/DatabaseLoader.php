<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Loader;

use LogicException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symkit\RoutingBundle\Factory\RouteFactory;
use Symkit\RoutingBundle\Repository\RouteRepository;
use Symkit\RoutingBundle\Resource\RoutingDatabaseResource;
use Throwable;

final class DatabaseLoader extends Loader
{
    private const string LOADER_TYPE = 'database';

    private bool $isLoaded = false;

    public function __construct(
        private readonly RouteRepository $routeRepository,
        private readonly RouteFactory $routeFactory,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function load(mixed $resource, ?string $type = null): mixed
    {
        if (true === $this->isLoaded) {
            throw new RuntimeException(\sprintf('Do not add the "%s" loader twice', self::LOADER_TYPE));
        }

        $collection = new RouteCollection();

        $state = $this->routeRepository->getRoutingState();
        $collection->addResource(new RoutingDatabaseResource($state['count'], $state['lastUpdate']));

        $entities = $this->routeRepository->findActivatedRoutes();
        foreach ($entities as $routeEntity) {
            try {
                if (!$routeEntity->isActive()) {
                    throw new LogicException('Route is not active');
                }

                $name = $routeEntity->getName();
                if (null === $name || '' === $name) {
                    $this->logger->warning('Skipping route with empty name');

                    continue;
                }

                $route = $this->routeFactory->createFromEntity($routeEntity);
                $route->compile();

                $collection->add($name, $route);
            } catch (Throwable $e) {
                $this->logger->error(\sprintf(
                    'Skipping route "%s" (path: "%s"): %s',
                    $routeEntity->getName(),
                    $routeEntity->getPath(),
                    $e->getMessage(),
                ));
            }
        }

        $this->isLoaded = true;

        return $collection;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return self::LOADER_TYPE === $type;
    }
}
