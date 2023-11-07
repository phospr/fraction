<?php

/**
 * This file is part of the Phospr Fraction package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr;

use InvalidArgumentException;

class CannotParseFractionFromString extends InvalidArgumentException
{
    public function __construct(string $fraction)
    {
        parent::__construct(sprintf(
            'Cannot parse fraction from string "%s"',
            $fraction,
        ));
    }
}
