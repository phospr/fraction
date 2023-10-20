<?php

/*
 * This file is part of the Phospr Fraction package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr;

use InvalidArgumentException;
use Phospr\Exception\Fraction\InvalidDenominatorException;

/**
 * Fraction
 *
 * Representation of a fraction, e.g. 3/4, 76/123, etc.
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  0.1.0
 */
class Fraction
{
    /**
     * From string regex pattern
     *
     * @var string
     */
    const PATTERN_FROM_STRING = '#^(-?\d+)(?:(?: (\d+))?/(\d+))?$#';

    private int $numerator;

    private int $denominator;

    public function __construct(int $numerator, int $denominator = 1)
    {
        if ($denominator < 1) {
            throw new InvalidDenominatorException(sprintf(
                'Denominator must be greater than zero.  Got %d',
                $denominator,
            ));
        }

        if (0 == $numerator) {
            $this->numerator = 0;
            $this->denominator = 1;

            return;
        }

        $this->numerator = $numerator;
        $this->denominator = $denominator;
    }

    public function __toString(): string
    {
        if (-1*$this->numerator === $this->denominator) {
            return '-1';
        }

        if (1 === $this->denominator) {
            return (string) $this->numerator;
        }

        if (abs($this->numerator) > abs($this->denominator)) {
            $whole = floor(abs($this->numerator) / $this->denominator);

            if ($this->numerator < 0) {
                $whole *= -1;
            }

            return sprintf('%d %d/%d',
                $whole,
                abs($this->numerator % $this->denominator),
                $this->denominator
            );
        }

        return sprintf('%d/%d',
            $this->numerator,
            $this->denominator
        );
    }

    public function getNumerator(): int
    {
        return $this->numerator;
    }

    public function getDenominator(): int
    {
        return $this->denominator;
    }

    /**
     * e.g. transform 2/4 into 1/2
     */
    public function simplify(): Fraction
    {
        // if the numerator is already zero, then we can't simplify any more
        if (0 == $this->numerator) {
            return $this;
        }

        if ($this->numerator === $this->denominator) {
            return new Fraction(1);
        }

        $gcd = $this->getGreatestCommonDivisor();

        return new self(
            $this->numerator /= $gcd,
            $this->denominator /= $gcd,
        );
    }

    private function getGreatestCommonDivisor(): int
    {
        $a = $this->numerator;
        $b = $this->denominator;

        // ensure no negative values
        $a = abs($a);
        $b = abs($b);

        // ensure $a is greater than $b
        if ($a < $b) {
            list($b, $a) = array($a, $b);
        }

        // see if $b is already the greatest common divisor
        $r = $a % $b;

        // if not, then keep trying
        while ($r > 0) {
            $a = $b;
            $b = $r;
            $r = $a % $b;
        }

        return $b;
    }

    public function multiply(Fraction $fraction): Fraction
    {
        $numerator = $this->getNumerator() * $fraction->getNumerator();
        $denominator = $this->getDenominator() * $fraction->getDenominator();

        return (new static($numerator, $denominator))->simplify();
    }

    public function divide(Fraction $fraction): Fraction
    {
        $numerator = $this->getNumerator() * $fraction->getDenominator();
        $denominator = $this->getDenominator() * $fraction->getNumerator();

        if ($denominator < 0) {
            $numerator *= -1;
            $denominator *= -1;
        }

        return (new static($numerator, $denominator))->simplify();
    }

    public function add(Fraction $fraction): Fraction
    {
        $numerator = ($this->getNumerator() * $fraction->getDenominator())
            + ($fraction->getNumerator() * $this->getDenominator());

        $denominator = $this->getDenominator()
            * $fraction->getDenominator();

        return (new static($numerator, $denominator))->simplify();
    }

    public function subtract(Fraction $fraction): Fraction
    {
        $numerator = ($this->getNumerator() * $fraction->getDenominator())
            - ($fraction->getNumerator() * $this->getDenominator());

        $denominator = $this->getDenominator()
            * $fraction->getDenominator();

        return (new static($numerator, $denominator))->simplify();
    }

    public function isInteger(): bool
    {
        return (1 === $this->simplify()->getDenominator());
    }

    /**
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     */
    public static function fromFloat(float $float): Fraction
    {
        if (is_int($float)) {
            return new self($float);
        }

        if (!is_numeric($float)) {
            throw new InvalidArgumentException(
                'Argument passed is not a numeric value.'
            );
        }

        // Make sure the float is a float not scientific notation.
        // Limit a max of 8 chars to prevent float errors
        $float = rtrim(sprintf('%.8F', $float), '0');

        // Find and grab the decimal space and everything after it
        $denominator = strstr($float, '.');

        // Pad a one with zeros for the length of the decimal places
        // ie  0.1 = 10; 0.02 = 100; 0.01234 = 100000;
        $denominator = (int) str_pad('1', strlen($denominator), '0');
        // Multiply to get rid of the decimal places.
        $numerator = (int) ($float*$denominator);

        return new self($numerator, $denominator);
    }

    /**
     * Create from string, e.g.
     *
     *     * 1/3
     *     * 1/20
     *     * 40
     *     * 3 4/5
     *     * 20 34/67
     */
    public static function fromString(string $string): Fraction
    {
        if (preg_match(self::PATTERN_FROM_STRING, trim($string), $matches)) {
            if (2 === count($matches)) {
                // whole number
                return new self((int) $matches[1]);
            } else {
                // either x y/z or x/y
                if ($matches[2]) {
                    // x y/z
                    $whole = new self((int) $matches[1]);

                    return $whole->add(new self(
                        (int) $matches[2],
                        (int) $matches[3]
                    ));
                }

                // x/y
                return new self((int) $matches[1], (int) $matches[3]);
            }
        }

        throw new InvalidArgumentException(sprintf(
            'Cannot parse "%s"',
            $string
        ));
    }

    public function toFloat(): float
    {
        return $this->getNumerator()/$this->getDenominator();
    }

    /**
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     */
    public function isSameValueAs(Fraction $fraction): bool
    {
        $thisSimplified = $this->simplify();
        $thatSimplified = $fraction->simplify();

        if ($thisSimplified->getNumerator() != $thatSimplified->getNumerator()) {
            return false;
        }

        if ($thisSimplified->getDenominator() != $thatSimplified->getDenominator()) {
            return false;
        }

        return true;
    }
}
