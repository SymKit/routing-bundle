<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symkit\FormBundle\Form\Type\ActiveInactiveType;
use Symkit\FormBundle\Form\Type\FormSectionType;
use Symkit\FormBundle\Form\Type\SitemapPriorityType;
use Symkit\RoutingBundle\Contract\RouteEntityInterface;

final class RouteType extends AbstractType
{
    public function __construct(
        private readonly string $entityClass,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $builder->getData();
        if ($data instanceof RouteEntityInterface && null === $data->getLinkedPage()) {
            $builder->add(
                $builder->create('general', FormSectionType::class, [
                    'inherit_data' => true,
                    'label' => 'form.section.general',
                    'section_icon' => 'heroicons:cog-6-tooth-20-solid',
                    'section_description' => 'form.section.general_description',
                ])
                    ->add('isActive', ActiveInactiveType::class, [
                        'help' => 'form.help.is_active',
                    ]),
            );
        }

        $builder->add(
            $builder->create('sitemap', FormSectionType::class, [
                'inherit_data' => true,
                'label' => 'form.section.sitemap',
                'section_icon' => 'heroicons:signal-20-solid',
                'section_description' => 'form.section.sitemap_description',
            ])
                ->add('sitemapPriority', SitemapPriorityType::class, [
                    'required' => false,
                    'help' => 'form.help.sitemap_priority',
                ])
                ->add('excludeFromSitemap', CheckboxType::class, [
                    'label' => 'form.label.exclude_from_sitemap',
                    'required' => false,
                    'help' => 'form.help.exclude_from_sitemap',
                ]),
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->entityClass,
            'translation_domain' => 'SymkitRoutingBundle',
        ]);
    }
}
