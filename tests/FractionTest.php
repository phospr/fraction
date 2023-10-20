<?php

/*
 * This file is part of the Phospr Fraction package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr\Tests;

use Phospr\Fraction;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Phospr\Exception\Fraction\InvalidDenominatorException;
use TypeError;

/**
 * FractionTest
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  0.1.0
 */
class FractionTest extends TestCase
{
    public static function bigFractionsProvider(): array
    {
        return array(
            array(PHP_INT_MAX, PHP_INT_MAX, '1'),
            array(-PHP_INT_MAX, PHP_INT_MAX, '-1'),
        );
    }

    /**
     * @dataProvider bigFractionsProvider
     */
    public function testBigFractions(int $numerator, int $denominator, string $expectedFraction): void
    {
        $fraction = new Fraction($numerator, $denominator);
        $expected = Fraction::fromString($expectedFraction);
        $this->assertTrue(
            abs($fraction->subtract($expected)->toFloat()) < PHP_FLOAT_EPSILON
        );
    }

    public function testANumeratorThatIsTooBig(): void
    {
        $this->expectException(TypeError::class);

        new Fraction(PHP_INT_MAX + 1);
    }

    public function testHalf(): void
    {
        $half = new Fraction(1, 2);

        $this->assertEquals('1/2', (string) $half);
        $this->assertSame(1, $half->getNumerator());
        $this->assertSame(2, $half->getDenominator());
    }

    /**
     * @dataProvider toStringProvider
     */
    public function testToString(int $numerator, ?int $denominator, string $string): void
    {
        if (null === $denominator) {
            $fraction = new Fraction($numerator);
        } else {
            $fraction = new Fraction($numerator, $denominator);
        }

        $this->assertSame($string, (string) $fraction);
    }

    public function toStringProvider(): array
    {
        return [
            [0, 2, '0'],
            [1, 2, '1/2'],
            [5, 2, '2 1/2'],
            // [15, 3, '15/3'], // re-enable w/ better whole number handling
            [13, 3, '4 1/3'],
            [1, null, '1'],
            [8, null, '8'],
            [8, 1, '8'],
            [-8, 1, '-8'],
            [-13, 3, '-4 1/3'],
            [1, 4, '1/4'],
            [7, 7, '7/7'],
            [7, 21, '7/21'],
        ];
    }


    /**
     * @dataProvider multiplicationProvider
     */
    public function testMultiply(int $numerator1, ?int $denominator1, int $numerator2, ?int $denominator2, string $string): void
    {
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
     * @dataProvider divisionProvider
     */
    public function testDivision(int $numerator1, ?int $denominator1, int $numerator2, ?int $denominator2, string $string): void
    {
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
     * @dataProvider additionProvider
     */
    public function testAddition(int $numerator1, ?int $denominator1, int $numerator2, ?int $denominator2, string $string): void
    {
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
     * @dataProvider subtractionProvider
     */
    public function testSubtraction(int $numerator1, ?int $denominator1, int $numerator2, ?int $denominator2, string $string): void
    {
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
     * @dataProvider isIntegerProvider
     */
    public function testIsInteger(int $numerator, ?int $denominator, bool $result): void
    {
        $fraction = new Fraction($numerator, $denominator);

        $this->assertSame($result, $fraction->isInteger());
    }

    /**
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     *
     * @dataProvider fromFloatProvider
     */
    public function testFromFloat(float $float, int $numerator, int $denominator): void
    {
        $fraction = Fraction::fromFloat($float)->simplify();

        $this->assertSame($numerator, $fraction->getNumerator());
        $this->assertSame($denominator, $fraction->getDenominator());
    }

    /**
     * @dataProvider toFloatProvider
     */
    public function testToFloat(int $numerator, int $denominator, float $result): void
    {
        if (null === $denominator) {
            $fraction = new Fraction($numerator);
        } else {
            $fraction = new Fraction($numerator, $denominator);
        }

        $this->assertTrue(
            abs($result - $fraction->toFloat()) < PHP_FLOAT_EPSILON
        );
    }

    public function testNegativeDenominator(): void
    {
        $this->expectException(InvalidDenominatorException::class);

        new Fraction(1, -1);
    }

    /**
     * @dataProvider fromStringProvider
     */
    public function testFromString(string $fromString, string $toString): void
    {
        $this->assertSame(
            $toString,
            (string) Fraction::fromString($fromString)->simplify()
        );
    }

    /**
     * @dataProvider fromStringExceptionProvider
     */
    public function testFromStringException(string $string): void
    {
        $this->expectException(InvalidArgumentException::class);

        Fraction::fromString($string);
    }

    /**
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     *
     * @dataProvider isSameValueAsProvider
     */
    public function testIsSameValueAs(int $numerator1, int $denominator1, int $numerator2, int $denominator2, bool $result): void
    {
        $fraction = new Fraction($numerator1, $denominator1);
        $fraction2 = new Fraction($numerator2, $denominator2);

        $this->assertSame(
            $result,
            $fraction->isSameValueAs($fraction2),
        );
    }

    public static function multiplicationProvider(): array
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

    public static function divisionProvider(): array
    {
        return array(
            array(1, 1, 1, 1, '1'),
            array(5, null, 2, null, '2 1/2'),
            array(6, 13, 2, 7, '1 8/13'),
            array(-1, 2, -1, 2, '1'),
            array(-1, 2, 1, 2, '-1'),
        );
    }

    public static function additionProvider(): array
    {
        return array(
            array(1, 1, 1, 1, '2'),
            array(1, 2, 1, 2, '1'),
            array(1, 2, 1, null, '1 1/2'),
            array(-1, 2, 1, null, '1/2'),
            array(2, 7, 3, 11, '43/77'),
        );
    }

    public static function subtractionProvider(): array
    {
        return array(
            array(1, 1, 1, 1, '0'),
            array(2, 3, 1, 2, '1/6'),
            array(2, 7, 3, 11, '1/77'),
            array(2, 7, 8, 11, '-34/77'),
            array(6, null, 4, 6, '5 1/3'),
        );
    }

    public static function isIntegerProvider(): array
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
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     */
    public static function fromFloatProvider(): array
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
        ];
    }

    public static function toFloatProvider(): array
    {
        return array(
            array(1, 1, 1),
            array(1, 1, 1.0),
            array(1, 4, 0.25),
            array(1, 8, 0.125),
        );
    }

    public static function fromStringProvider(): array
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
            ['40/20', '2'],
            ['-40/20', '-2'],
            ['40/2', '20'],
            ['-40/2', '-20'],
        ];
    }

    public static function fromStringExceptionProvider(): array
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
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     */
    public static function isSameValueAsProvider(): array
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
