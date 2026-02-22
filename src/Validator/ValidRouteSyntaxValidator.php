<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symkit\RoutingBundle\Contract\RouteEntityInterface;
use Symkit\RoutingBundle\Factory\RouteFactory;
use Throwable;

final class ValidRouteSyntaxValidator extends ConstraintValidator
{
    public function __construct(
        private readonly RouteFactory $routeFactory,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidRouteSyntax) {
            throw new UnexpectedTypeException($constraint, ValidRouteSyntax::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof RouteEntityInterface) {
            throw new UnexpectedTypeException($value, RouteEntityInterface::class);
        }

        try {
            $route = $this->routeFactory->createFromEntity($value);
            $route->compile();
        } catch (Throwable $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ pattern }}', $value->getPath() ?? '')
                ->setParameter('{{ error }}', $e->getMessage())
                ->setTranslationDomain('SymkitRoutingBundle')
                ->atPath('path')
                ->addViolation();
        }
    }
}
