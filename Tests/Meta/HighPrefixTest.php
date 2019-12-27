<?php
declare(strict_types=1);

namespace Bungle\FrameworkBundle\Tests\Meta;

use PHPUnit\Framework\TestCase;
use Bungle\FrameworkBundle\Meta\HighPrefix;
use Bungle\FrameworkBundle\Meta\SimpleEntityLocator;
use Bungle\FrameworkBundle\Annotation\AnnotationNotDefinedException;

final class HighPrefixTest extends TestCase
{
    public function testEmptyGetHigh(): void
    {
        self::expectException(\InvalidArgumentException::class);

        $prefixer = new HighPrefix(new SimpleEntityLocator([]));
        $prefixer->getHigh('Foo');
    }

    public function testEmptyGetClass(): void
    {
        self::expectException(\InvalidArgumentException::class);

        $prefixer = new HighPrefix(new SimpleEntityLocator([]));
        $prefixer->getClass('Foo');
        self::assertEmpty($prefixer->getPrefixes());
    }

    public function test(): void
    {
        $prefixer = new HighPrefix(new SimpleEntityLocator([Order::class]));
        self::assertEquals(Order::class, $prefixer->getClass('ord'));
        self::assertEquals('ord', $prefixer->getHigh(Order::class));
        self::assertEquals(['ord'], $prefixer->getPrefixes());
    }

    public function testDupHigh(): void
    {
        self::expectException(\AssertionError::class);

        new HighPrefix(new SimpleEntityLocator([
          Order::class,
          Order::class,
         ]));
    }

    public function testIgnoreAnnoNotDefined(): void
    {
        $prefixer = new HighPrefix(new SimpleEntityLocator([
          Order::class,
          self::class,
        ]));
        self::assertEquals(['ord'], $prefixer->getPrefixes());
    }
}
