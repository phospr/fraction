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
     * Test fromFloat()
     *
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @since  0.2.0
     *
     * @dataProvider fromFloatProvider
     */
    public function testFromFloat($float, $numerator, $denominator)
    {
        $fraction = Fraction::fromFloat($float);

        $this->assertSame($numerator, $fraction->getNumerator());
        $this->assertSame($denominator, $fraction->getDenominator());
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
     * Test fromString
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.4.0
     *
     * @dataProvider fromStringProvider
     */
    public function testFromString($fromString, $toString)
    {
        $this->assertSame(
            $toString,
            (string) Fraction::fromString($fromString)
        );
    }

    /**
     * Test fromString exception
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.4.0
     *
     * @expectedException \InvalidArgumentException
     * @dataProvider fromStringExceptionProvider
     */
    public function testFromStringException($string)
    {
        Fraction::fromString($string);
    }

    /**
     * Test isSameValueAs
     *
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @since 1.1.0
     *
     * @dataProvider isSameValueAsProvider
     */
    public function testIsSameValueAs(
        $numerator1,
        $denominator1,
        $numerator2,
        $denominator2,
        $result
    ) {
        $fraction = new Fraction($numerator1, $denominator1);

        $this->assertSame($result, $fraction->isSameValueAs(new Fraction($numerator2, $denominator2)));
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
     * From float provider
     *
     * @author Christopher Tatro <ctatro@janeiredale.com>
     * @since  0.2.0
     *
     * @return array
     */
    public static function fromFloatProvider()
    {
        return [
            [12345.1234, 61725617, 5000],
            [9999.9999, 99999999, 10000],
            [0.000001, 1, 1000000],
            [0.0215011, 215011, 10000000],
            [0.0000025000, 1, 400000],
            [0.00001, 1, 100000],
            [1.25, 5, 4],
            [1.3245, 2649, 2000],
            [0.23, 23, 100],
            [1, 1, 1],
            [5.5000, 11, 2],
            [6.375, 51, 8],
            [235.63247, 23563247, 100000],
            // Test some negatives
            [-0.0215011, -215011, 10000000],
            [-0.0000025000, -1, 400000],
            [-0.00001, -1, 100000],
            [-1.25, -5, 4],
            [-1.3245, -2649, 2000],
            // Test some strings
            ['1', 1, 1],
            ['5', 5, 1],
            ['5.5000', 11, 2],
            ['6.375', 51, 8],
            ['-6.375', -51, 8],
            ['-1.25', -5, 4],
            ['-0.00001', -1, 100000],
        ];
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
            array(1, 1, 1.0),
            array(1, 4, 0.25),
            array(1, 8, 0.125),
        );
    }

    /**
     * fromString provider
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.4.0
     *
     * @return array
     */
    public static function fromStringProvider()
    {
        return [
            ['1/3', '1/3'],
            ['-1/3', '-1/3'],
            [' 1/3 ', '1/3'],
            ['1/3 ', '1/3'],
            [' 1/3', '1/3'],
            ['1/20', '1/20'],
            ['-1/20', '-1/20'],
            ['40', '40'],
            ['-40', '-40'],
            ['3 4/5', '3 4/5'],
#            ['-3 4/5', '-3 4/5'],
#            ['20 34/67', '20 34/67'],
#            ['-20 34/67', '-20 34/67'],
            ['40/20', '2'],
            ['-40/20', '-2'],
            ['40/2', '20'],
            ['-40/2', '-20'],
        ];
    }

    /**
     * fromString exception provider
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.4.0
     *
     * @return array
     */
    public static function fromStringExceptionProvider()
    {
        return [
            ['tom'],
            ['1 4/3 6'],
            ['1/4 3/6'],
            ['1 /3'],
            ['-1/-3'],
            ['1/'],
            ['/1'],
            ['10 4'],
        ];
    }

    /**
     * isSameValueAsProvider
     *
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @since  1.1.0
     *
     * @return array
     */
    public static function isSameValueAsProvider()
    {
        return [
            [1, 2, 1, 2, true],
            [1, 3, 2, 6, true],
            [2, 2, 3, 3, true],
            [4, 2, 8, 4, true],
            [1, 1, 1, 2, false],
            [3, 2, 4, 2, false],
            [1, 4, 1, 2, false],
            [1650, 2, 825, 1, true],
            [-1, 3, -2, 6, true],
            [-6550, 50, -131, 1, true],
            [-4, 2, -4, 2, true],
            [4, 2, -4, 2, false],
            [-4, 2, 4, 2, false],
            [4, 2, 4, 2, true],
            [2605020, 159780620, 130251, 7989031, true],
            [-2605020, 159780620, -130251, 7989031, true],
        ];
    }
}
