CHANGELOG

2.0
---

### Breaking Changes

#### `InvalidDenominatorException` has been removed

* We use typehints now
* Denominator can be negative now
* Denominator still cannot be zero, and throws `DenominatorCannotBeZero`

#### `InvalidNumeratorException` has been removed

* We use typehints now

#### Fractions are no longer simplified in the constructor

```php
// 1.2.1
echo new Fraction(2, 4); // 1/2

// 2.0.0
echo new Fraction(2, 4); // 2/4
```

The `simplify()` method is now public:

```php
// 2.0.0
echo new Fraction(2, 4)->simplify(); // 1/2
```
