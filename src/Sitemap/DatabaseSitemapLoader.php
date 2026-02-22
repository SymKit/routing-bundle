<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Sitemap;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symkit\RoutingBundle\Repository\RouteRepository;
use Symkit\SitemapBundle\Contract\SitemapLoaderInterface;
use Symkit\SitemapBundle\Contract\SitemapPriorityCalculatorInterface;
use Symkit\SitemapBundle\Model\SitemapUrl;

final readonly class DatabaseSitemapLoader implements SitemapLoaderInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private RouteRepository $repository,
        private SitemapPriorityCalculatorInterface $priorityCalculator,
    ) {
    }

    public function count(): int
    {
        return $this->repository->countActivatedRoutes();
    }

    /**
     * @return iterable<SitemapUrl>
     */
    public function load(int $limit, int $offset): iterable
    {
        $routes = $this->repository->findActivatedRoutes($limit, $offset);

        foreach ($routes as $route) {
            if (null !== $route->getLinkedPage()) {
                continue;
            }

            if ($route->isExcludeFromSitemap()) {
                continue;
            }

            $path = $route->getPath() ?? '';
            if (str_contains($path, '{')) {
                continue;
            }

            $methods = $route->getMethods();
            if ([] !== $methods && !\in_array('GET', $methods, true)) {
                continue;
            }

            $name = $route->getName();
            if (null === $name || '' === $name) {
                continue;
            }

            $priority = null !== $route->getSitemapPriority()
                ? (string) $route->getSitemapPriority()
                : $this->priorityCalculator->calculate($path);

            yield new SitemapUrl(
                loc: $this->urlGenerator->generate($name, [], UrlGeneratorInterface::ABSOLUTE_URL),
                lastmod: $route->getUpdatedAt(),
                changefreq: 'daily',
                priority: $priority,
            );
        }
    }
}
