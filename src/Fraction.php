<?php

/*
 * This file is part of the Phospr Fraction package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr;

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
     * numerator
     *
     * @var integer
     */
    private $numerator;

    /**
     * denominator
     *
     * @var initeger
     */
    private $denominator;

    /**
     * __construct
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @param integer $numerator
     * @param integer $denominator
     */
    public function __construct($numerator, $denominator = 1)
    {
        if (!is_int($numerator)) {
            throw new InvalidNumeratorException(
                'Numerator must be an integer'
            );
        }

        if (!is_int($denominator)) {
            throw new InvalidDenominatorException(
                'Denominator must be an integer'
            );
        }

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

        // if the gmp_gcd function is available, use it, on the assumption
        // that it will perform better
        // http://php.net/manual/en/function.gmp-gcd.php
        if (function_exists('gmp_gcd')) {
            return gmp_gcd($a, $b);
        }

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

        return new static($numerator, $denominator);
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
}
