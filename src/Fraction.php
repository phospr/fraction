<?php

/*
 * This file is part of the Phospr Fraction package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr;

use InvalidArgumentException;

/**
 * Fraction
 *
 * Representation of a fraction, e.g. 3/4, 76/123, 2 3/4 etc.
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  0.1.0
 */
final class Fraction
{
    /**
     * THe pattern used to convert a string to a Fraction
     *
     * @var string
     */
    const PATTERN_FROM_STRING = '#^(-?\d+)(?:(?: (-?\d+))?/(-?\d+))?$#';

    private ?int $wholeNumber = null;

    private ?int $numerator = null;

    private ?int $denominator = null;

    public function __construct(int $first, ?int $second = null, ?int $third = null)
    {
        if ($second === null && $third === null) {
            // only $first was set
            $this->wholeNumber = $first;

            return;
        }

        if ($third === null) {
            // only $first and $second were set
            $this->numerator = $first;
            $this->denominator = $second;
        } else {
            // $first, $second and $third were set
            $this->wholeNumber = $first;
            $this->numerator = $second;
            $this->denominator = $third;
        }

        if ($this->denominator === 0) {
            throw new DenominatorCannotBeZero();
        }

        if (
            $this->wholeNumber !== null &&
            abs($this->numerator) >= abs($this->denominator)
        ) {
            throw new FractionCannotBeBothMixedAndImproper();
        }
    }

    public function isWholeNumber(): bool
    {
        return $this->numerator === null;
    }

    public function isProper(): bool
    {
        return $this->wholeNumber === null &&
            abs($this->numerator) < abs($this->denominator);
    }

    public function isImproper(): bool
    {
        return $this->wholeNumber === null &&
            abs($this->numerator) >= abs($this->denominator);
    }

    public function isMixed(): bool
    {
        return $this->wholeNumber !== null && $this->numerator !== null;
    }

    public function __toString(): string
    {
        if ($this->numerator === null) {
            return (string) $this->wholeNumber;
        }

        if ($this->wholeNumber === null) {
            return sprintf('%d/%d',
                $this->numerator,
                $this->denominator
            );
        }

        return sprintf('%d %d/%d',
            $this->wholeNumber,
            $this->numerator,
            $this->denominator
        );
    }

    public function getNumerator(): ?int
    {
        return $this->numerator;
    }

    public function getDenominator(): ?int
    {
        return $this->denominator;
    }

    /**
     * e.g. transform 2/4 into 1/2 or 21/4 into 5 1/4
     */
    public function simplify(): Fraction
    {
        if ($this->isWholeNumber()) {
            // can't be simplified any further
            return $this;
        }

        $f = $this;

        if ($this->isMixed()) {
            $f = $this->toImproper();
        }

        if ($f->numerator === 0) {
            return new Fraction(0);
        }

        if ($f->numerator == $f->denominator) {
            return new Fraction(1);
        }

        if (abs($f->numerator) == abs($f->denominator)) {
            return new Fraction(-1);
        }

        $f = $f->reduce();

        if ($f->denominator === 1) {
            return new Fraction($f->numerator);
        }

        if ($f->isImproper()) {
            return $f->toMixed();
        }

        $numerator = $f->numerator;
        $denominator = $f->denominator;

        // make sure negative sign is on the numerator
        if ($numerator > 0 && $denominator < 0) {
            $numerator *= -1;
            $denominator *= -1;
        }

        return new Fraction($numerator, $denominator);
    }

    /**
     * e.g. 1 1/2 => 3/2
     */
    public function toImproper(): Fraction
    {
        if ($this->isImproper()) {
            // already improper
            return $this;
        }

        if ($this->isWholeNumber()) {
            return new Fraction($this->wholeNumber, 1);
        }

        $isPositive = 0 <= $this->wholeNumber
            * $this->numerator
            * $this->denominator;

        $numerator = abs($this->numerator) +
            (abs($this->wholeNumber) * abs($this->denominator));

        if (!$isPositive) {
            $numerator *= -1;
        }

        return new Fraction($numerator, $this->denominator);
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

    public function multiply(Fraction $f): Fraction
    {
        $f1 = $this;
        $f2 = $f;

        if ($f1->isMixed() || $f1->isWholeNumber()) {
            $f1 = $f1->toImproper();
        }

        if ($f2->isMixed() || $f2->isWholeNumber()) {
            $f2 = $f2->toImproper();
        }

        $numerator = $f1->numerator * $f2->numerator;
        $denominator = $f1->denominator * $f2->denominator;

        return (new static($numerator, $denominator))->simplify();
    }

    public function divide(Fraction $f): Fraction
    {
        $f1 = $this;
        $f2 = $f;

        if ($f1->isMixed() || $f1->isWholeNumber()) {
            $f1 = $f1->toImproper();
        }

        if ($f2->isMixed() || $f2->isWholeNumber()) {
            $f2 = $f2->toImproper();
        }

        $numerator = $f1->numerator * $f2->denominator;
        $denominator = $f1->denominator * $f2->numerator;

        if ($denominator < 0) {
            $numerator *= -1;
            $denominator *= -1;
        }

        return (new static($numerator, $denominator))->simplify();
    }

    public function add(Fraction $fraction): Fraction
    {
        $numerator = ($this->numerator * $fraction->denominator)
            + ($fraction->numerator * $this->denominator);

        $denominator = $this->denominator
            * $fraction->denominator;

        return (new static($numerator, $denominator))->simplify();
    }

    public function subtract(Fraction $other): Fraction
    {
        $minuend = $this->simplify()->toImproper();
        $subtrahend = $other->simplify()->toImproper();

        print_r([
            'minuend' => (string) $minuend,
            'subtrahend' => (string) $subtrahend,
        ]);

        $numerator = ($minuend->numerator * $subtrahend->denominator)
            - ($subtrahend->numerator * $minuend->denominator);

        $denominator = $minuend->denominator
            * $subtrahend->denominator;

        print_r([$numerator, $denominator]);

        if ($denominator === 0) {
            return new Fraction($numerator);
        }

        return (new Fraction($numerator, $denominator))->simplify();
    }

    public function isInteger(): bool
    {
        return $this->simplify()->numerator === null;
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

        return (new Fraction($numerator, $denominator))->simplify();
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
                    return new self(
                        (int) $matches[1],
                        (int) $matches[2],
                        (int) $matches[3],
                    );
                }

                // x/y
                return new self((int) $matches[1], (int) $matches[3]);
            }
        }

        throw new CannotParseFractionFromString($string);
    }

    public function toFloat(): float
    {
        return $this->numerator/$this->denominator;
    }

    /**
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     */
    public function isSameValueAs(Fraction $fraction): bool
    {
        $thisSimplified = $this->simplify();
        $thatSimplified = $fraction->simplify();

        return
            $thisSimplified->wholeNumber === $thatSimplified->wholeNumber &&
            $thisSimplified->numerator === $thatSimplified->numerator &&
            $thisSimplified->denominator === $thatSimplified->denominator
        ;
    }

    private function getWholeNumberCombinedWithNumerator(): int
    {
        $numerator = $this->numerator +
            (abs($this->wholeNumber) * $this->denominator);

        if ($this->wholeNumber >= 0) {
            return $numerator;
        } else {
            return -1 * $numerator;
        }
    }

    public function reduce(): Fraction
    {
        $gcd = $this->getGreatestCommonDivisor();

        $numerator = $this->numerator / $gcd;
        $denominator = $this->denominator / $gcd;

        return new Fraction($numerator, $denominator);
    }

    public function toMixed(): Fraction
    {
        $wholeNumber = floor(abs($this->numerator) / $this->denominator);

        if ($this->numerator > 0) {
            $numerator = $this->numerator - ($wholeNumber * $this->denominator);
        } elseif ($this->numerator < 0) {
            $numerator = -1 * ($this->numerator + ($wholeNumber * $this->denominator));
            $wholeNumber *= -1;
        }

        return new Fraction($wholeNumber, $numerator, $this->denominator);
    }
}
