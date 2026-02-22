# SymkitRoutingBundle

[![CI](https://github.com/symkit/routing-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/symkit/routing-bundle/actions)
[![Latest Version](https://img.shields.io/packagist/v/symkit/routing-bundle.svg)](https://packagist.org/packages/symkit/routing-bundle)
[![PHPStan Level 9](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg)](https://phpstan.org/)

Dynamic database-driven routing for Symfony. Manage routes in the database with native performance via Symfony's routing cache.

## Requirements

- PHP 8.2+
- Symfony 7.0+ or 8.0+
- `doctrine/doctrine-bundle` (Doctrine ORM)
- `symkit/crud-bundle` (for admin)
- `symkit/sitemap-bundle` (sitemap; disable via `symkit_routing.sitemap.enabled` if not needed)
- `symkit/search-bundle` (global search; disable via `symkit_routing.search.enabled` if not needed)

## Installation

```bash
composer require symkit/routing-bundle
```

Register the bundle in `config/bundles.php`:

```php
return [
    // ...
    Symkit\RoutingBundle\RoutingBundle::class => ['all' => true],
];
```

## Configuration

Example with all options (`config/packages/symkit_routing.yaml`):

```yaml
symkit_routing:
    enabled: true
    entity_class: Symkit\RoutingBundle\Entity\Route  # Your entity FQCN (must implement RouteEntityInterface)
    admin:
        enabled: true
        path_prefix: /admin/routes
    sitemap:
        enabled: true
    search:
        enabled: true
    listener:
        invalidate_sitemap_on_change: true
```

## Loading database routes

In `config/routes.yaml` (or your main routes file):

```yaml
database_routes:
    resource: .
    type: database
```

## Admin routes

If `admin.enabled` is true, mount the bundle's admin routes with the configured prefix:

```yaml
# config/routes.yaml
_symkit_routing_admin:
    resource: '@SymkitRoutingBundle/config/routes.yaml'
    prefix: /admin/routes   # or use %symkit_routing.admin.path_prefix% if you expose it as a parameter
```

Route names: `admin_routes_list`, `admin_routes_edit`.

## Overriding the entity

1. Create an entity implementing `Symkit\RoutingBundle\Contract\RouteEntityInterface` (or extend `Symkit\RoutingBundle\Entity\Route`).
2. Map it with Doctrine (annotations/attributes or XML).
3. Set `entity_class` in config to your FQCN.

Your entity can add relations (e.g. a linked Page) and implement `getLinkedPage()` / `getLinkedPageLabel()` for sitemap and search behaviour.

## Validation

The default entity uses:

- `ValidRouteSyntax`: validates path and options (Symfony route pattern).
- `UniqueEntity`: unique `name` and `path`.

Messages use the `SymkitRoutingBundle` translation domain (see `translations/`). For UniqueEntity, configure your validation mapping to use that domain if needed.

## Cache

The bundle registers a `RoutingDatabaseResource` and a resource checker. The routing cache is invalidated only when the set of routes or their last update timestamp changes.

## Sitemap and search

- **Sitemap**: when `sitemap.enabled` is true, active routes (without parameters, GET allowed, not excluded) are exposed via `SymkitSitemapBundle` (loader index `database`).
- **Search**: when `search.enabled` is true, routes are searchable in the global search (SymkitSearchBundle).

## Contributing

- Run the quality pipeline: `make quality` (cs-check, phpstan, deptrac, tests, infection).
- Full CI: `make ci` (adds security-check).

## License

MIT.
