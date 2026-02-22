<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symkit\CrudBundle\Controller\AbstractCrudController;
use Symkit\MenuBundle\Attribute\ActiveMenu;
use Symkit\MetadataBundle\Attribute\Breadcrumb;
use Symkit\MetadataBundle\Attribute\Seo;
use Symkit\RoutingBundle\Contract\RouteEntityInterface;
use Symkit\RoutingBundle\Form\RouteType;

final class RouteController extends AbstractCrudController
{
    private const TRANSLATION_DOMAIN = 'SymkitRoutingBundle';

    public function __construct(
        private readonly string $entityClass,
        private readonly TranslatorInterface $translator,
        private readonly string $adminPathPrefix = '/admin/routes',
    ) {
    }

    protected function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getAdminPathPrefix(): string
    {
        return $this->adminPathPrefix;
    }

    protected function getFormClass(): string
    {
        return RouteType::class;
    }

    protected function getRoutePrefix(): string
    {
        return 'admin_routes';
    }

    protected function configureListFields(): array
    {
        return [
            'name' => [
                'label' => $this->translator->trans('admin.field.name', [], self::TRANSLATION_DOMAIN),
                'sortable' => true,
                'cell_class' => 'font-mono text-sm',
            ],
            'path' => [
                'label' => $this->translator->trans('admin.field.path', [], self::TRANSLATION_DOMAIN),
                'sortable' => true,
                'cell_class' => 'font-mono text-sm',
            ],
            'isActive' => [
                'label' => $this->translator->trans('admin.field.active', [], self::TRANSLATION_DOMAIN),
                'sortable' => true,
                'template' => '@SymkitCrud/crud/field/boolean.html.twig',
            ],
            'updatedAt' => [
                'label' => $this->translator->trans('admin.field.updated', [], self::TRANSLATION_DOMAIN),
                'sortable' => true,
                'template' => '@SymkitCrud/crud/field/date.html.twig',
            ],
            'actions' => [
                'label' => '',
                'template' => '@SymkitCrud/crud/field/actions.html.twig',
                'edit_route' => 'admin_routes_edit',
                'header_class' => 'text-right',
                'cell_class' => 'text-right',
            ],
        ];
    }

    protected function configureSearchFields(): array
    {
        return ['name', 'path'];
    }

    #[Breadcrumb(context: 'admin')]
    #[Seo(title: 'Routes', description: 'Manage database-driven routes.')]
    #[ActiveMenu('admin', 'routes')]
    public function list(Request $request): Response
    {
        return $this->renderIndex($request, [
            'page_title' => $this->translator->trans('admin.page_title.list', [], self::TRANSLATION_DOMAIN),
            'page_description' => $this->translator->trans('admin.page_description.list', [], self::TRANSLATION_DOMAIN),
            'create_route' => false,
        ]);
    }

    #[Breadcrumb(context: 'admin', items: [['label' => 'Routes', 'route' => 'admin_routes_list']])]
    #[Seo(title: 'Edit Route', description: 'Update route settings.')]
    #[ActiveMenu('admin', 'routes')]
    public function edit(RouteEntityInterface $route, Request $request): Response
    {
        return $this->renderEdit($route, $request, [
            'page_title' => $this->translator->trans('admin.page_title.edit_name', ['%name%' => $route->getName() ?? ''], self::TRANSLATION_DOMAIN),
            'page_description' => $this->translator->trans('admin.page_description.edit', [], self::TRANSLATION_DOMAIN),
            'show_delete' => false,
        ]);
    }
}
