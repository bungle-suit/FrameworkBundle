<?php

declare(strict_types=1);

namespace Bungle\Framework\Tests\Annotations;

use Bungle\Framework\Annotations\LogicName;
use PHPUnit\Framework\TestCase;

final class LogicNameTest extends TestCase
{
    public function testResolveClassName()
    {
        self::assertEquals('Order Bill', LogicName::resolveClassName(Entity::class));
    }

    public function testResolveClassNameNoLogicName()
    {
        self::assertEquals('LogicNameTest', LogicName::resolveClassName(self::class));
    }

    public function testGetShortClassName()
    {
        self::assertEquals('Foo', LogicName::getShortClassName('Foo'));
        self::assertEquals('Entity', LogicName::getShortClassName(Entity::class));
    }

    public function testResolvePropertyNames()
    {
        self::assertEquals(
            [
              'id' => 'ID',
              'count' => 'Counter',
              'name' => 'name',
            ],
            LogicName::resolvePropertyNames(Entity::class),
        );
    }

    public function testResolveDerivedPropertyNames(): void
    {
        self::assertEquals(
            [
              'id' => 'ID',
              'count' => 'New Counter',
              'name' => 'name',
              'address' => '地址',
            ],
            LogicName::resolvePropertyNames(Derived::class)
        );
    }

    public function testResolveTraitsPropertyNames(): void
    {
        self::assertEquals(
            [
            'count' => '数量',
            'modifier' => '修改人',
            'modifyTime' => '修改时间',
            ],
            LogicName::resolvePropertyNames(MixedTraits::class),
        );
    }
}
