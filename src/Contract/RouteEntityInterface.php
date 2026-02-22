<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Contract;

use DateTimeImmutable;

interface RouteEntityInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function getPath(): ?string;

    public function getController(): ?string;

    public function isActive(): ?bool;

    /** @return array<int, string> */
    public function getMethods(): array;

    /** @return array<int, string> */
    public function getSchemes(): array;

    /** @return array<string, mixed> */
    public function getDefaults(): array;

    /** @return array<string, string> */
    public function getRequirements(): array;

    /** @return array<string, mixed> */
    public function getOptions(): array;

    public function getHost(): ?string;

    public function getCondition(): ?string;

    public function getSitemapPriority(): ?float;

    public function isExcludeFromSitemap(): bool;

    public function getUpdatedAt(): ?DateTimeImmutable;

    /**
     * Optional linked resource (e.g. a Page). When not null, the route may be excluded from sitemap.
     */
    public function getLinkedPage(): ?object;

    /**
     * Optional label for the linked page (e.g. for search result subtitle). Null when no linked page.
     */
    public function getLinkedPageLabel(): ?string;
}
