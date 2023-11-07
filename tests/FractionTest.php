<?php

/*
 * This file is part of the Phospr Fraction package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr\Tests;

use Phospr\CannotParseFractionFromString;
use Phospr\Fraction;
use PHPUnit\Framework\TestCase;
use Phospr\DenominatorCannotBeZero;
use Phospr\FractionCannotBeBothMixedAndImproper;

/**
 * FractionTest
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  0.1.0
 */
class FractionTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_not_accept_a_zero_denominator(): void
    {
        $this->expectException(DenominatorCannotBeZero::class);

        new Fraction(1, 0);
    }

    /**
     * @test
     */
    public function it_cannot_be_both_mixed_and_improper(): void
    {
        $this->expectException(FractionCannotBeBothMixedAndImproper::class);

        new Fraction(1, 1, 1);
    }

    /**
     * @test
     * @dataProvider toAndFromStringProvider
     */
    public function it_can_be_created_from_a_string(Fraction $f, string $string): void
    {
        $this->assertEquals($f, Fraction::fromString($string));
    }

    /**
     * @test
     * @dataProvider toAndFromStringProvider
     */
    public function it_can_be_converted_to_a_string(Fraction $f, string $string): void
    {
        $this->assertSame($string, (string) $f);
    }

    public function toAndFromStringProvider(): array
    {
        return [
            [new Fraction(0),          '0'],
            [new Fraction(1),          '1'],
            [new Fraction(-1),         '-1'],
            [new Fraction(1, 1),       '1/1'],
            [new Fraction(-1, 1),      '-1/1'],
            [new Fraction(-1, -1),     '-1/-1'],
            [new Fraction(1, -1),      '1/-1'],
            [new Fraction(0, 2),       '0/2'],
            [new Fraction(0, -2),      '0/-2'],
            [new Fraction(1, 2),       '1/2'],
            [new Fraction(-1, 2),      '-1/2'],
            [new Fraction(-1, -2),     '-1/-2'],
            [new Fraction(1, -2),      '1/-2'],
            [new Fraction(5, 2),       '5/2'],
            [new Fraction(-5, 2),      '-5/2'],
            [new Fraction(-5, -2),     '-5/-2'],
            [new Fraction(5, -2),      '5/-2'],
            [new Fraction(15, 3),      '15/3'],
            [new Fraction(-15, 3),     '-15/3'],
            [new Fraction(-15, -3),    '-15/-3'],
            [new Fraction(1, 2, 3),    '1 2/3'],
            [new Fraction(-1, 2, 3),   '-1 2/3'],
            [new Fraction(-1, -2, -3), '-1 -2/-3'],
            [new Fraction(1, -2, -3),  '1 -2/-3'],
            [new Fraction(1, 2, -3),   '1 2/-3'],
            [new Fraction(4, 6, -12),  '4 6/-12'],
        ];
    }

    /**
     * @test
     * @dataProvider invalidStringProvider
     */
    public function it_cannot_be_created_from_a_poorly_formatted_string(string $string): void
    {
        $this->expectException(CannotParseFractionFromString::class);

        Fraction::fromString($string);
    }

    public function invalidStringProvider(): array
    {
        return [
            [''],
            ['1 1'],
            ['1 2'],
            ['1- 2'],
            ['1 -2'],
            ['1/2-'],
            ['1/2/'],
            ['1/2 1/2'],
            ['one'],
            ['half'],
        ];
    }

    /**
     * @test
     * @dataProvider typeProvider
     */
    public function it_should_what_type_it_is(string $f, bool $isWholeNumber, bool $isProper, bool $isImproper, bool $isMixed): void
    {
        $this->assertSame($isProper, Fraction::fromString($f)->isProper());
        $this->assertSame($isImproper, Fraction::fromString($f)->isImproper());
        $this->assertSame($isWholeNumber, Fraction::fromString($f)->isWholeNumber());
        $this->assertSame($isMixed, Fraction::fromString($f)->isMixed());
    }

    public function typeProvider(): array
    {
        return [
            // fraction  whole  proper  improper  mixed
            ['0',        true,  false,  false,    false],
            ['1',        true,  false,  false,    false],
            ['-1',       true,  false,  false,    false],
            ['2',        true,  false,  false,    false],
            ['-2',       true,  false,  false,    false],
            ['0/2',      false, true,   false,    false],
            ['1/2',      false, true,   false,    false],
            ['-1/2',     false, true,   false,    false],
            ['2/3',      false, true,   false,    false],
            ['-2/3',     false, true,   false,    false],
            ['1/1',      false, false,  true,     false],
            ['-1/1',     false, false,  true,     false],
            ['-1/-1',    false, false,  true,     false],
            ['1/-1',     false, false,  true,     false],
            ['2/2',      false, false,  true,     false],
            ['-2/2',     false, false,  true,     false],
            ['2/1',      false, false,  true,     false],
            ['-2/1',     false, false,  true,     false],
            ['3/2',      false, false,  true,     false],
            ['-3/2',     false, false,  true,     false],
            ['4/2',      false, false,  true,     false],
            ['-4/2',     false, false,  true,     false],
            ['1000/1',   false, false,  true,     false],
            ['-1000/1',  false, false,  true,     false],
            ['1 2/3',    false, false,  false,    true],
            ['-1 2/3',   false, false,  false,    true],
            ['1 2/-3',   false, false,  false,    true],
            ['-1 -2/-3', false, false,  false,    true],
            ['1 -2/-3',  false, false,  false,    true],
        ];
    }

    /**
     * @test
     * @dataProvider improperToMixedProvider
     */
    public function it_can_be_converted_from_improper_to_mixed(string $improper, string $mixed): void
    {
        $this->assertEquals(
            Fraction::fromString($mixed),
            Fraction::fromString($improper)->toMixed(),
        );
    }

    public function improperToMixedProvider(): array
    {
        return [
            ['5/3', '1 2/3'],
            ['-5/3', '-1 2/3'],
            ['5/3', '1 2/3'],
            ['3/2', '1 1/2'],
        ];
    }

    /**
     * @test
     * @dataProvider mixedToImproperProvider
     */
    public function it_can_be_converted_from_mixed_to_improper(string $mixed, string $improper): void
    {
        $this->assertEquals(
            Fraction::fromString($improper),
            Fraction::fromString($mixed)->toImproper(),
        );
    }

    public function mixedToImproperProvider(): array
    {
        return [
            ['1 2/3', '5/3'],
            ['-1 2/3', '-5/3'],
            ['-1 -2/3', '5/3'],
            ['1 1/2', '3/2'],
        ];
    }

    /**
     * @test
     */
    public function it_can_be_reduced(): void
    {
        $f = new Fraction(2,4);

        $this->assertEquals(new Fraction(1,2), $f->reduce());
    }

    /**
     * @test
     * @dataProvider it_can_be_simplified_provider
     */
    public function it_can_be_simplified(Fraction $f, string $expected): void
    {
        $this->assertSame($expected, (string) $f->simplify());
    }

    public function it_can_be_simplified_provider(): array
    {
        return [
          // whole numbers
          [new Fraction(0), '0'],
          [new Fraction(1), '1'],
          [new Fraction(-1), '-1'],
          [new Fraction(2), '2'],
          [new Fraction(-2), '-2'],
          [new Fraction(PHP_INT_MAX), (string) PHP_INT_MAX],
          [new Fraction(PHP_INT_MIN), (string) PHP_INT_MIN],
          // fractions
          [new Fraction(0, 1), '0'],
          [new Fraction(0, -1), '0'],
          [new Fraction(1, 1), '1'],
          [new Fraction(-1, 1), '-1'],
          [new Fraction(1, -1), '-1'],
          [new Fraction(-1, -1), '1'],
          [new Fraction(2, 2), '1'],
          [new Fraction(-2, 2), '-1'],
          [new Fraction(2, -2), '-1'],
          [new Fraction(1, 2), '1/2'],
          [new Fraction(-1, 2), '-1/2'],
          [new Fraction(1, -2), '-1/2'],
          [new Fraction(3, 2), '1 1/2'],
          [new Fraction(-3, 2), '-1 1/2'],
          [new Fraction(5, 3), '1 2/3'],
          [new Fraction(-5, 3), '-1 2/3'],
          [new Fraction(6, 4), '1 1/2'],
          [new Fraction(-6, 4), '-1 1/2'],
          [new Fraction(21, 3), '7'],
          [new Fraction(-21, 3), '-7'],
          [new Fraction(21, 4), '5 1/4'],
          [new Fraction(-21, 4), '-5 1/4'],
          [new Fraction(42, 8), '5 1/4'],
          [new Fraction(-42, 8), '-5 1/4'],
          [new Fraction(123451234, 10000), '12345 617/5000'],
          // mixed fractions
          [new Fraction(0, 2, 3), '2/3'],
          [new Fraction(1, 2, 3), '1 2/3'],
          [new Fraction(-1, 2, 3), '-1 2/3'],
        ];
    }

    /**
     * @test
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     * @dataProvider isSameValueAsProvider
     */
    public function it_can_tell_if_is_the_same_value_as_another_fraction(string $f1, string $f2, bool $isSameValueAs): void
    {
        $this->assertSame(
            $isSameValueAs,
            Fraction::fromString($f1)->isSameValueAs(Fraction::fromString($f2)),
        );
    }

    /**
     * @author Christopher Tatro <c.m.tatro@gmail.com>
     */
    public static function isSameValueAsProvider(): array
    {
        return [
            // f1        f2     same value as?
            ['1/2',      '1/2', true],
            ['1/3',      '2/6', true],
            ['2/2',      '3/3', true],
            ['4/2',      '8/4', true],
            ['1/1',      '1/2', false],
            ['3/2',      '4/2', false],
            ['1/4',      '1/2', false],
            ['1650/2',   '825/1', true],
            ['-1/3',     '-2/6', true],
            ['-6550/50', '-131/1', true],
            ['-4/2',     '-4/2', true],
            ['4/2',      '-4/2', false],
            ['-4/2',     '4/2', false],
            ['4/2',      '4/2', true],
            ['2605020/159780620', '130251/7989031', true],
            ['-2605020/159780620', '-130251/7989031', true],
        ];
    }

    /**
     * @test
     * @dataProvider multiplicationDivisionProvider
     */
    public function it_can_be_multiplied_by_another_fraction(Fraction $f1, Fraction $f2, Fraction $product): void
    {
        $this->assertTrue($f1->multiply($f2)->isSameValueAs($product));
    }

    /**
     * @test
     * @dataProvider multiplicationDivisionProvider
     */
    public function it_can_be_divided_by_another_fraction(Fraction $f1, Fraction $f2, Fraction $product): void
    {
        $this->assertTrue($product->divide($f2)->isSameValueAs($f1));
    }

    public function multiplicationDivisionProvider(): array
    {
        return [
            // a x b = c
            // c = b / a
            [new Fraction(1),    new Fraction(1),      new Fraction(1)],
            [new Fraction(-1),   new Fraction(1),      new Fraction(-1)],
            [new Fraction(-1),   new Fraction(-1),     new Fraction(1)],
            [new Fraction(1),    new Fraction(1, 2),   new Fraction(1, 2)],
            [new Fraction(-1),   new Fraction(1, 2),   new Fraction(-1, 2)],
            [new Fraction(-1),   new Fraction(-1, 2),  new Fraction(1, 2)],
            [new Fraction(-1),   new Fraction(-1, -2), new Fraction(-1, 2)],
            [new Fraction(1, 2), new Fraction(1, 2),   new Fraction(1, 4)],
            [new Fraction(4, 8), new Fraction(3, 12),  new Fraction(1, 8)],
            //[new Fraction(1, 4, 8), new Fraction(3, 12), new Fraction(1, 8)],
        ];
    }

//  /**
//   * @dataProvider additionProvider
//   */
//  public function testAddition(int $numerator1, ?int $denominator1, int $numerator2, ?int $denominator2, string $string): void
//  {
//      if (null === $denominator1) {
//          $fraction1 = new Fraction($numerator1);
//      } else {
//          $fraction1 = new Fraction($numerator1, $denominator1);
//      }

//      if (null === $denominator2) {
//          $fraction2 = new Fraction($numerator2);
//      } else {
//          $fraction2 = new Fraction($numerator2, $denominator2);
//      }

//      $this->assertSame($string, (string) $fraction1->add($fraction2));
//  }

//  /**
//   * @test
//   * @dataProvider subtractionProvider
//   */
//  public function it_can_subtract_another_fraction_from_itself(Fraction $a, Fraction $b, Fraction $result): void
//  {
//      $this->assertTrue($a->subtract($b)->isSameValueAs($result));
//  }

//  public function subtractionProvider(): array
//  {
//      return [
//       //   [new Fraction(1), new Fraction(1), new Fraction(0)],
//          [new Fraction(-1), new Fraction(1), new Fraction(-2)],
//      ];
//      //return array(
//      //    array(1, 1, 1, 1, '0'),
//      //    array(2, 3, 1, 2, '1/6'),
//      //    array(2, 7, 3, 11, '1/77'),
//      //    array(2, 7, 8, 11, '-34/77'),
//      //    array(6, null, 4, 6, '5 1/3'),
//      //);
//  }

//  /**
//   * @author Christopher Tatro <c.m.tatro@gmail.com>
//   * @test
//   * @dataProvider it_can_be_converted_from_a_float_provider
//   */
//  public function it_can_be_converted_from_a_float(float $float, string $fractionAsString): void
//  {
//      $fraction = Fraction::fromFloat($float);

//      $this->assertSame($fractionAsString, (string) $fraction);
//  }

//  /**
//   * @author Christopher Tatro <c.m.tatro@gmail.com>
//   */
//  public function it_can_be_converted_from_a_float_provider(): array
//  {
//      return [
//          [0, '0'],
//          [1, '1'],
//          [10, '10'],
//          [-10, '-10'],
//          [10.000, '10'],
//          [-10.000, '-10'],
//          [-1, '-1'],
//          [1.0, '1'],
//          [-1.0, '-1'],
//          [0.5, '1/2'],
//          [-0.5, '-1/2'],
//          [0.000001, '1/1000000'],
//          [-0.000001, '-1/1000000'],
//          [12345.1234, '12345 617/5000'],
//          [-12345.1234, '-12345 617/5000'],
//          [9999.9999, '9999 9999/10000'],
//          [-9999.9999, '-9999 9999/10000'],
//          [1.25, '1 1/4'],
//          [-1.25, '-1 1/4'],
//          [1.33, '1 33/100'],
//          [-1.33, '-1 33/100'],
//          [6.375, '6 3/8'],
//          [-6.375, '-6 3/8'],
//          [1.3245, '1 649/2000'],
//          [-1.3245, '-1 649/2000'],
//      ];
//  }

//  /**
//   * @dataProvider toFloatProvider
//   */
//  public function testToFloat(int $numerator, int $denominator, float $result): void
//  {
//      if (null === $denominator) {
//          $fraction = new Fraction($numerator);
//      } else {
//          $fraction = new Fraction($numerator, $denominator);
//      }

//      $this->assertTrue(
//          abs($result - $fraction->toFloat()) < PHP_FLOAT_EPSILON
//      );
//  }

//  /**
//   * @author Christopher Tatro <c.m.tatro@gmail.com>
//   *
//   * @dataProvider isSameValueAsProvider
//   */
//  public function testIsSameValueAs(int $numerator1, int $denominator1, int $numerator2, int $denominator2, bool $result): void
//  {
//      $fraction = new Fraction($numerator1, $denominator1);
//      $fraction2 = new Fraction($numerator2, $denominator2);

//      $this->assertSame(
//          $result,
//          $fraction->isSameValueAs($fraction2),
//      );
//  }

//  public static function additionProvider(): array
//  {
//      return array(
//          array(1, 1, 1, 1, '2'),
//          array(1, 2, 1, 2, '1'),
//          array(1, 2, 1, null, '1 1/2'),
//          array(-1, 2, 1, null, '1/2'),
//          array(2, 7, 3, 11, '43/77'),
//      );
//  }

//  public static function toFloatProvider(): array
//  {
//      return array(
//          array(1, 1, 1),
//          array(1, 1, 1.0),
//          array(1, 4, 0.25),
//          array(1, 8, 0.125),
//      );
//  }

//  public static function fromStringExceptionProvider(): array
//  {
//      return [
//          ['tom'],
//          ['1 4/3 6'],
//          ['1/4 3/6'],
//          ['1 /3'],
//          ['-1/-3'],
//          ['1/'],
//          ['/1'],
//          ['10 4'],
//      ];
//  }
}
