<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symkit\RoutingBundle\RoutingBundle;

final class RoutingBundleBootTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        restore_exception_handler();
    }

    public function testBundleBootsWithDisabledConfig(): void
    {
        $routesPath = __DIR__.'/routes.yaml';
        $kernel = new RoutingTestKernel('test', true, $routesPath);
        $kernel->boot();

        $container = $kernel->getContainer();
        self::assertTrue($container->has('kernel'));

        $kernel->shutdown();
    }
}

/**
 * @internal
 */
final class RoutingTestKernel extends Kernel
{
    public function __construct(
        string $environment,
        bool $debug,
        private readonly string $routesPath = '',
    ) {
        parent::__construct($environment, $debug);
    }

    public function registerBundles(): iterable
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new RoutingBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $routesPath = $this->routesPath;
        $loader->load(static function (ContainerBuilder $container) use ($routesPath): void {
            $container->loadFromExtension('framework', [
                'test' => true,
                'secret' => 'test',
                'http_method_override' => false,
                'handle_all_throwables' => true,
                'php_errors' => ['log' => true],
                'router' => ['utf8' => true, 'resource' => $routesPath],
            ]);
            $container->loadFromExtension('symkit_routing', ['enabled' => false]);
        });
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/symkit_routing_bundle_test_'.uniqid();
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir();
    }
}
