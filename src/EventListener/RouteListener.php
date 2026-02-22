<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\EventListener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symkit\RoutingBundle\Contract\RouteEntityInterface;
use Symkit\SitemapBundle\Event\SitemapInvalidateEvent;

final readonly class RouteListener
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function postPersist(RouteEntityInterface $route): void
    {
        $this->invalidateSitemap();
    }

    public function postUpdate(RouteEntityInterface $route): void
    {
        $this->invalidateSitemap();
    }

    public function postRemove(RouteEntityInterface $route): void
    {
        $this->invalidateSitemap();
    }

    private function invalidateSitemap(): void
    {
        $this->eventDispatcher->dispatch(new SitemapInvalidateEvent());
    }
}
