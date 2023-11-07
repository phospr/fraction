<?php

/**
 * This file is part of the Phospr Fraction package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr;

use InvalidArgumentException;

class FractionCannotBeBothMixedAndImproper extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Fraction cannot be both mixed and improper');
    }
}
