<?php
declare(strict_types=1);

namespace Bungle\Framework\Tests;

use ArrayIterator;
use Bungle\Framework\FP;
use LogicException;
use PHPUnit\Framework\TestCase;

class FPTest extends TestCase
{
    public function testAttr(): void
    {
        $f = FP::attr('name');
        $o = (object)['id' => 1, 'name' => 'foo'];
        self::assertEquals('foo', $f($o));
    }

    public function testGetter(): void
    {
        $o = new class {
            public function getName(): string
            {
                return 'bar';
            }
        };
        $f = FP::getter('getName');
        self::assertEquals('bar', $f($o));
    }

    public function testT(): void
    {
        $f = FP::t();
        self::assertTrue($f());
    }

    public function testF(): void
    {
        $f = FP::f();
        self::assertFalse($f());
    }

    public function testGroup(): void
    {
        $arr = range(0, 10);
        self::assertEquals([
            0 => [0, 2, 4, 6, 8, 10],
            1 => [1, 3, 5, 7, 9],
        ], FP::group(fn(int $v) => $v % 2, $arr));
    }

    public function testEqualGroup(): void
    {
        $arr = range(1, 10);
        self::assertEquals([
            [1, 6],
            [2, 7],
            [3, 8],
            [4, 9],
            [5, 10],
        ], FP::equalGroup(fn(int $a, int $b) => $a + 5 === $b, $arr));
    }

    public function testAny(): void
    {
        // empty always return false
        self::assertFalse(FP::any(fn (int $v) => true, []));

        self::assertTrue(FP::any(fn(int $v) => $v % 2 === 0, [1, 3, 6, 9]));
        self::assertFalse(FP::any(fn(int $v) => $v % 2 === 0, [1, 9, 111]));
    }

    public function testAll(): void
    {
        // empty always return true
        self::assertTrue(FP::all(fn (int $v) => false, []));

        self::assertFalse(FP::all(fn(int $v) => $v % 2 === 0, [1, 3, 6, 9]));
        self::assertTrue(FP::all(fn(int $v) => $v % 2 === 1, [1, 9, 111]));
    }

    public function testIsEmpty(): void
    {
        self::assertTrue(FP::isEmpty([]));
        self::assertFalse(FP::isEmpty([1]));
    }

    public function testIdentity(): void
    {
        self::assertEquals(3, FP::identity(3));
    }

    public function testZero(): void
    {
        self::assertEquals(0, FP::zero());
    }

    public function testConstant(): void
    {
        $one = FP::constant(1);
        self::assertEquals(1, $one());

        $foo = FP::constant('foo');
        self::assertEquals('foo', $foo());
    }

    public function testInitVariable(): void
    {
        $a = 'foo';
        self::assertEquals('foo', FP::initVariable($a, FP::constant(0)));
        self::assertEquals('foo', $a);

        $a = '';
        self::assertEquals('foo', FP::initVariable($a, FP::constant('foo')));
        self::assertEquals('foo', $a);

        $a = 'foo';
        self::assertEquals('bar', FP::initVariable($a, FP::constant('bar'), fn($v) => $v === 'foo'));
    }

    public function testInitProperty(): void
    {
        $o = (object)[];
        self::assertEquals('foo', FP::initProperty($o, 'a', FP::constant('foo')));
        self::assertEquals('foo', $o->a);
        self::assertEquals('foo', FP::initProperty($o, 'a', FP::constant('foo')));
        self::assertEquals('foo', $o->a);

        self::assertEquals('bar', FP::initProperty($o, 'a', FP::constant('bar'), fn($v) => $v === 'foo'));
        self::assertEquals('bar', $o->a);

        // Ignore fIsUninitialized if the property is unset.
        self::assertEquals('blah', FP::initProperty($o, 'b', FP::constant('blah'), fn($v) => $v === 'foo'));
    }

    public function testInitArrayItem(): void
    {
        $arr = [];
        self::assertEquals('foo', FP::initArrayItem($arr, 3, FP::constant('foo')));
        self::assertEquals('foo', $arr[3]);

        self::assertEquals('bar', FP::initArrayItem($arr, 'a', FP::constant('bar'), fn($v) => $v === 'foo'));
        self::assertEquals(345, FP::initArrayItem($arr, 'a', FP::constant(345), fn($v) => $v === 'bar'));
        self::assertEquals('345', $arr['a']);
    }

    public function testToKeyed(): void
    {
        self::assertEquals([], FP::toKeyed(fn(int $v): int => $v, []));

        $arr = [['foo', 1], ['bar', 2]];
        self::assertEquals([
            'foo' => ['foo', 1],
            'bar' => ['bar', 2],
        ], FP::toKeyed(fn($v) => $v[0], $arr));
    }

    public function testGetOrCreate(): void
    {
        $arr = [];
        $f = fn(int $k) => (string)$k;
        self::assertEquals('3', FP::getOrCreate($arr, 3, $f));
        self::assertEquals([3 => '3'], $arr);
        self::assertEquals('3', FP::getOrCreate($arr, 3, $f));
        self::assertEquals([3 => '3'], $arr);
    }

    public function testFirstIterator(): void
    {
        $emptyIter = new ArrayIterator([]);
        self::assertNull(FP::firstOrNull(FP::t(), $emptyIter));
        self::assertEquals(33, FP::first(FP::t(), $emptyIter, 33));
        self::assertEquals(
            4,
            FP::firstOrNull(
                fn($v) => $v === 4,
                new ArrayIterator(range(1, 10))
            )
        );
        self::assertNull(FP::firstOrNull(FP::f(), range(1, 10)));
    }

    public function testFirstArray(): void
    {
        self::assertNull(FP::firstOrNull(fn (int $v) => true, []));
        self::assertEquals(33, FP::first(FP::t(), [], 33));
        self::assertEquals(
            4,
            FP::firstOrNull(
                fn($v) => $v === 4,
                range(1, 10)
            )
        );
    }

    public function testNotNull(): void
    {
        self::assertEquals(1, FP::notNull(1));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Expect value not null');

        FP::notNull(null);
    }

    public function testNotNullWithMessage(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('foo');

        FP::notNull(null, 'foo');
    }
}
