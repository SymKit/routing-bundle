<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symkit\RoutingBundle\Controller\Admin\RouteController;
use Symkit\RoutingBundle\Entity\Route;
use Symkit\RoutingBundle\EventListener\RouteListener;
use Symkit\RoutingBundle\Factory\RouteFactory;
use Symkit\RoutingBundle\Form\RouteType;
use Symkit\RoutingBundle\Loader\DatabaseLoader;
use Symkit\RoutingBundle\Repository\RouteRepository;
use Symkit\RoutingBundle\Resource\RoutingDatabaseResourceChecker;
use Symkit\RoutingBundle\Search\RouteSearchProvider;
use Symkit\RoutingBundle\Sitemap\DatabaseSitemapLoader;
use Symkit\RoutingBundle\Validator\ValidRouteSyntaxValidator;

class RoutingBundle extends AbstractBundle
{
    protected string $extensionAlias = 'symkit_routing';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->booleanNode('enabled')
                    ->defaultTrue()
                    ->info('Master switch to enable or disable the bundle features.')
                ->end()
                ->scalarNode('entity_class')
                    ->defaultValue(Route::class)
                    ->cannotBeEmpty()
                    ->info('FQCN of the route entity (must implement RouteEntityInterface).')
                ->end()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                            ->info('Enable the admin CRUD controller for routes.')
                        ->end()
                        ->scalarNode('path_prefix')
                            ->defaultValue('/admin/routes')
                            ->cannotBeEmpty()
                            ->info('URL prefix for admin routes (list and edit).')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('sitemap')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                            ->info('Register the database sitemap loader.')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('search')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                            ->info('Register the route search provider for global search.')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('listener')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('invalidate_sitemap_on_change')
                            ->defaultTrue()
                            ->info('Dispatch sitemap invalidate event when a route is persisted/updated/removed.')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param array{
     *     enabled: bool,
     *     entity_class: class-string,
     *     admin: array{enabled: bool, path_prefix: string},
     *     sitemap: array{enabled: bool},
     *     search: array{enabled: bool},
     *     listener: array{invalidate_sitemap_on_change: bool},
     * } $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (!$config['enabled']) {
            return;
        }

        $services = $container->services();
        $services->defaults()
            ->autowire()
            ->autoconfigure();

        $entityClass = $config['entity_class'];

        $services->set(RouteRepository::class)
            ->arg('$entityClass', $entityClass)
            ->tag('doctrine.repository_service');

        $services->set(DatabaseLoader::class)
            ->tag('routing.loader');

        $services->set(RouteFactory::class);

        $services->set(RoutingDatabaseResourceChecker::class)
            ->tag('config_cache.resource_checker');

        $services->set(RouteType::class)
            ->arg('$entityClass', $entityClass);

        $services->set(ValidRouteSyntaxValidator::class);

        if ($config['admin']['enabled']) {
            $services->set(RouteController::class)
                ->arg('$entityClass', $entityClass)
                ->arg('$adminPathPrefix', $config['admin']['path_prefix'])
                ->tag('controller.service_arguments');
        }

        if ($config['sitemap']['enabled']) {
            $services->set(DatabaseSitemapLoader::class)
                ->tag('symkit_sitemap.loader', ['index' => 'database']);
        }

        if ($config['search']['enabled']) {
            $services->set(RouteSearchProvider::class)
                ->tag('symkit_search.provider');
        }

        if ($config['listener']['invalidate_sitemap_on_change']) {
            $services->set(RouteListener::class)
                ->tag('doctrine.orm.entity_listener', ['entity' => $entityClass, 'event' => 'postPersist', 'method' => 'postPersist'])
                ->tag('doctrine.orm.entity_listener', ['entity' => $entityClass, 'event' => 'postUpdate', 'method' => 'postUpdate'])
                ->tag('doctrine.orm.entity_listener', ['entity' => $entityClass, 'event' => 'postRemove', 'method' => 'postRemove']);
        }
    }
}
