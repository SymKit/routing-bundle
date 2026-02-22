<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Search;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symkit\RoutingBundle\Repository\RouteRepository;
use Symkit\SearchBundle\Contract\SearchProviderInterface;
use Symkit\SearchBundle\Model\SearchResult;

final readonly class RouteSearchProvider implements SearchProviderInterface
{
    public function __construct(
        private RouteRepository $routeRepository,
        private UrlGeneratorInterface $urlGenerator,
        private \Symfony\Contracts\Translation\TranslatorInterface $translator,
    ) {
    }

    public function search(string $query): iterable
    {
        $routes = $this->routeRepository->findForGlobalSearch($query);

        foreach ($routes as $route) {
            $subtitle = $route->getPath() ?? '';
            $label = $route->getLinkedPageLabel();
            if (null !== $label && '' !== $label) {
                $subtitle .= ' • '.$this->translator->trans('search.page_label', ['%title%' => $label], 'SymkitRoutingBundle');
            }

            $id = $route->getId();
            $badge = $route->isActive()
                ? $this->translator->trans('search.badge.active', [], 'SymkitRoutingBundle')
                : $this->translator->trans('search.badge.inactive', [], 'SymkitRoutingBundle');
            yield new SearchResult(
                title: $route->getName() ?? '',
                subtitle: $subtitle,
                url: $this->urlGenerator->generate('admin_routes_edit', ['id' => $id]),
                icon: 'heroicons:link-20-solid',
                badge: $badge,
            );
        }
    }

    public function getCategory(): string
    {
        return $this->translator->trans('search.category', [], 'SymkitRoutingBundle');
    }

    public function getPriority(): int
    {
        return 20;
    }
}
