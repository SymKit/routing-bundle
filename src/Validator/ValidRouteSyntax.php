<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
class ValidRouteSyntax extends Constraint
{
    public string $message = 'validator.route_syntax_invalid';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
