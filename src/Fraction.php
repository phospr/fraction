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
use Phospr\Exception\Fraction\InvalidNumeratorException;

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

    /**
     * numerator
     *
     * @var integer
     */
    private $numerator;

    /**
     * denominator
     *
     * @var integer
     */
    private $denominator;

    /**
     * __construct
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     */
    public function __construct(int $numerator, int $denominator = 1)
    {
        list($numerator, $denominator) = $this->checkLimits(
            $numerator, $denominator
        );

        if ((int) $denominator < 1) {
            throw new InvalidDenominatorException(
                'Denominator must be an integer greater than zero'
            );
        }

        if (0 == $numerator) {
            $this->numerator = (int) 0;
            $this->denominator = (int) 1;

            return;
        }

        $this->numerator = (int) $numerator;
        $this->denominator = (int) $denominator;

        $this->simplify();
    }

    /**
     * Check limits
     *
     * @param mixed $numerator
     * @param mixed $denominator
     * @return array
     */
    protected function checkLimits($numerator, $denominator)
    {
        if (($max = max(abs($numerator), abs($denominator))) < PHP_INT_MAX) {
            return [$numerator, $denominator];
        }

        $divisor = min(
            abs($this->getDivisor($max)),
            abs($numerator),
            abs($denominator)
        );

        return [
            intval($numerator / $divisor),
            intval($denominator / $divisor),
        ];
    }

    /**
     * Get divisor
     *
     * @param mixed $number
     * @return integer
     */
    protected function getDivisor($number)
    {
        $divisor = 1;

        while ($number >= PHP_INT_MAX) {
            $divisor *= 10;
            $number /= 10;
        }

        return $divisor;
    }

    /**
     * __toString
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->numerator === $this->denominator) {
            return '1';
        }

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

    /**
     * Get numerator
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return integer
     */
    public function getNumerator()
    {
        return $this->numerator;
    }

    /**
     * Get denominator
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return integer
     */
    public function getDenominator()
    {
        return $this->denominator;
    }

    /**
     * Simplify
     *
     * e.g. transform 2/4 into 1/2
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return integer
     */
    private function simplify()
    {
        $gcd = $this->getGreatestCommonDivisor();

        $this->numerator /= $gcd;
        $this->denominator /= $gcd;
    }

    /**
     * Get greatest common divisor
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return integer
     */
    private function getGreatestCommonDivisor()
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

    /**
     * Multiply this fraction by a given fraction
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @param Fraction $fraction
     *
     * @return Fraction
     */
    public function multiply(Fraction $fraction)
    {
        $numerator = $this->getNumerator() * $fraction->getNumerator();
        $denominator = $this->getDenominator() * $fraction->getDenominator();

        return new static($numerator, $denominator);
    }

    /**
     * Divide this fraction by a given fraction
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @param Fraction $fraction
     *
     * @return Fraction
     */
    public function divide(Fraction $fraction)
    {
        $numerator = $this->getNumerator() * $fraction->getDenominator();
        $denominator = $this->getDenominator() * $fraction->getNumerator();

        if ($denominator < 0) {
            $numerator *= -1;
        }

        return new static($numerator, abs($denominator));
    }

    /**
     * Add this fraction to a given fraction
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @param Fraction $fraction
     *
     * @return Fraction
     */
    public function add(Fraction $fraction)
    {
        $numerator = ($this->getNumerator() * $fraction->getDenominator())
            + ($fraction->getNumerator() * $this->getDenominator());

        $denominator = $this->getDenominator()
            * $fraction->getDenominator();

        return new static($numerator, $denominator);
    }

    /**
     * Subtract a given fraction from this fraction
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @param Fraction $fraction
     *
     * @return Fraction
     */
    public function subtract(Fraction $fraction)
    {
        $numerator = ($this->getNumerator() * $fraction->getDenominator())
            - ($fraction->getNumerator() * $this->getDenominator());

        $denominator = $this->getDenominator()
            * $fraction->getDenominator();

        return new static($numerator, $denominator);
    }

    /**
     * Whether or not this fraction is an integer
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return boolean
     */
    public function isInteger()
    {
        return (1 === $this->getDenominator());
    }

    /**
     * Create from float
     *
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @since  0.2.0
     *
     * @param float $float
     *
     * return Fraction
     */
    public static function fromFloat($float)
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
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.4.0
     *
     * @param string $string
     *
     * return Fraction
     */
    public static function fromString($string)
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

    /**
     * Get value as float
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return float
     */
    public function toFloat()
    {
        return $this->getNumerator()/$this->getDenominator();
    }

    /**
     * isSameValueAs
     *
     * ValueObject comparison
     *
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @since 1.1.0
     *
     * @param Fraction $fraction
     *
     * @return bool
     */
    public function isSameValueAs(Fraction $fraction)
    {
        if ($this->getNumerator() != $fraction->getNumerator()) {
            return false;
        }

        if ($this->getDenominator() != $fraction->getDenominator()) {
            return false;
        }

        return true;
    }
}
