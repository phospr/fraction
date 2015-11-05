<?php

/*
 * This file is part of the Phospr Fraction package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr\Tests;

use Phospr\Fraction;

/**
 * FractionTest
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  0.1.0
 */
class FractionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Some assumptions I have made while developing this package
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     */
    public function testAssumptions()
    {
        // floats are not the same as integers
        $this->assertNotSame(1, 1.0);
        $this->assertEquals(1, 1.0);

        // floats are not the same as integers
        $this->assertNotSame(1, 4.0/4.0);
        $this->assertEquals(1, 4.0/4.0);

        // integers are not the same as strings
        $this->assertNotSame(1, '1');
        $this->assertEquals(1, '1');
    }

    /**
     * Test half
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     */
    public function testHalf()
    {
        $half = new Fraction(1, 2);

        $this->assertEquals('1/2', (string) $half);
        $this->assertSame(1, $half->getNumerator());
        $this->assertSame(2, $half->getDenominator());
    }

    /**
     * Test __toString()
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @dataProvider toStringProvider
     */
    public function testToString(
        $numerator,
        $denominator,
        $string
    ) {
        if (null === $denominator) {
            $fraction = new Fraction($numerator);
        } else {
            $fraction = new Fraction($numerator, $denominator);
        }

        $this->assertSame($string, (string) $fraction);
    }

    /**
     * Test multiplication
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @dataProvider multiplicationProvider
     */
    public function testMultiply(
        $numerator1,
        $denominator1,
        $numerator2,
        $denominator2,
        $string
    ) {
        if (null === $denominator1) {
            $fraction1 = new Fraction($numerator1);
        } else {
            $fraction1 = new Fraction($numerator1, $denominator1);
        }

        if (null === $denominator2) {
            $fraction2 = new Fraction($numerator2);
        } else {
            $fraction2 = new Fraction($numerator2, $denominator2);
        }

        $this->assertSame($string, (string) $fraction1->multiply($fraction2));
    }

    /**
     * Test division
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @dataProvider divisionProvider
     */
    public function testDivision(
        $numerator1,
        $denominator1,
        $numerator2,
        $denominator2,
        $string
    ) {
        if (null === $denominator1) {
            $fraction1 = new Fraction($numerator1);
        } else {
            $fraction1 = new Fraction($numerator1, $denominator1);
        }

        if (null === $denominator2) {
            $fraction2 = new Fraction($numerator2);
        } else {
            $fraction2 = new Fraction($numerator2, $denominator2);
        }

        $this->assertSame($string, (string) $fraction1->divide($fraction2));
    }

    /**
     * Test addition
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @dataProvider additionProvider
     */
    public function testAddition(
        $numerator1,
        $denominator1,
        $numerator2,
        $denominator2,
        $string
    ) {
        if (null === $denominator1) {
            $fraction1 = new Fraction($numerator1);
        } else {
            $fraction1 = new Fraction($numerator1, $denominator1);
        }

        if (null === $denominator2) {
            $fraction2 = new Fraction($numerator2);
        } else {
            $fraction2 = new Fraction($numerator2, $denominator2);
        }

        $this->assertSame($string, (string) $fraction1->add($fraction2));
    }

    /**
     * Test subtraction
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @dataProvider subtractionProvider
     */
    public function testSubtraction(
        $numerator1,
        $denominator1,
        $numerator2,
        $denominator2,
        $string
    ) {
        if (null === $denominator1) {
            $fraction1 = new Fraction($numerator1);
        } else {
            $fraction1 = new Fraction($numerator1, $denominator1);
        }

        if (null === $denominator2) {
            $fraction2 = new Fraction($numerator2);
        } else {
            $fraction2 = new Fraction($numerator2, $denominator2);
        }

        $this->assertSame($string, (string) $fraction1->subtract($fraction2));
    }

    /**
     * Test isInteger()
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @dataProvider isIntegerProvider
     */
    public function testIsInteger(
        $numerator,
        $denominator,
        $result
    ) {
        if (null === $denominator) {
            $fraction = new Fraction($numerator);
        } else {
            $fraction = new Fraction($numerator, $denominator);
        }

        $this->assertSame($result, $fraction->isInteger());
    }

    /**
     * Test toFloat()
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @dataProvider toFloatProvider
     */
    public function testToFloat(
        $numerator,
        $denominator,
        $result
    ) {
        if (null === $denominator) {
            $fraction = new Fraction($numerator);
        } else {
            $fraction = new Fraction($numerator, $denominator);
        }

        $this->assertSame($result, $fraction->toFloat());
    }

    /**
     * Test bad denominator
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @expectedException Phospr\Exception\Fraction\InvalidDenominatorException
     * @dataProvider badIntegerProvider
     */
    public function testBadDenominator($denominator)
    {
        $fraction = new Fraction(1, $denominator);
    }

    /**
     * Test negative denominator
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @expectedException Phospr\Exception\Fraction\InvalidDenominatorException
     */
    public function testNegativeDenominator()
    {
        $fraction = new Fraction(1, -1);
    }

    /**
     * Test bad numerator
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @expectedException Phospr\Exception\Fraction\InvalidNumeratorException
     * @dataProvider badIntegerProvider
     */
    public function testBadNumerator($numerator)
    {
        $fraction = new Fraction($numerator, 1);
    }

    /**
     * __toString provider
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return array
     */
    public static function toStringProvider()
    {
        return array(
            array(0, 2, '0'),
            array(1, 2, '1/2'),
            array(5, 2, '2 1/2'),
            array(15, 3, '5'),
            array(13, 3, '4 1/3'),
            array(1, null, '1'),
            array(8, null, '8'),
            array(8, 1, '8'),
            array(-8, 1, '-8'),
            array(-13, 3, '-4 1/3'),
            array(1, 4, '1/4'),
            array(7, 7, '1'),
            array(7, 21, '1/3'),
        );
    }

    /**
     * Bad integer provider
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return array
     */
    public static function badIntegerProvider()
    {
        return array(
            array(null),
            array(0.4),
            array(.5),
            array(''),
            array(' '),
            array(1.0),
            array('5'),
            array('5i'),
            array('hello'),
        );
    }

    /**
     * Multiplication provider
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return array
     */
    public static function multiplicationProvider()
    {
        return array(
            array(1, 1, 1, 1, '1'),
            array(-1, 1, -1, 1, '1'),
            array(-1, 1, 1, 1, '-1'),
            array(1, 2, 1, 2, '1/4'),
            array(-1, 2, 1, 2, '-1/4'),
            array(2, 1, 1, 2, '1'),
            array(-2, 1, 1, 2, '-1'),
            array(4, 8, 3, 12, '1/8'),
        );
    }

    /**
     * Division provider
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return array
     */
    public static function divisionProvider()
    {
        return array(
            array(1, 1, 1, 1, '1'),
            array(5, null, 2, null, '2 1/2'),
            array(6, 13, 2, 7, '1 8/13'),
        );
    }

    /**
     * Addition provider
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return array
     */
    public static function additionProvider()
    {
        return array(
            array(1, 1, 1, 1, '2'),
            array(1, 2, 1, 2, '1'),
            array(1, 2, 1, null, '1 1/2'),
            array(-1, 2, 1, null, '1/2'),
            array(2, 7, 3, 11, '43/77'),
        );
    }

    /**
     * Subtraction provider
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return array
     */
    public static function subtractionProvider()
    {
        return array(
            array(1, 1, 1, 1, '0'),
            array(2, 3, 1, 2, '1/6'),
            array(2, 7, 3, 11, '1/77'),
            array(2, 7, 8, 11, '-34/77'),
            array(6, null, 4, 6, '5 1/3'),
        );
    }

    /**
     * Is integer provider
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return array
     */
    public static function isIntegerProvider()
    {
        return array(
            array(1, 1, true),
            array(0, 1, true),
            array(4, 1, true),
            array(14, 14, true),
            array(14, 7, true),
            array(-14, 7, true),
        );
    }

    /**
     * To float provider
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.1.0
     *
     * @return array
     */
    public static function toFloatProvider()
    {
        return array(
            array(1, 1, 1),
            array(1, 4, 0.25),
            array(1, 8, 0.125),
        );
    }
}
