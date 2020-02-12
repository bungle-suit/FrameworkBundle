<?php
declare(strict_types=1);

namespace Bungle\FrameworkBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Bungle\FrameworkBundle\Entity\EntityMeta;
use Bungle\FrameworkBundle\Entity\EntityPropertyMeta;
use Bungle\FrameworkBundle\Exception\Exceptions;

final class EntityMetaTest extends TestCase
{
    public function testName(): void
    {
        $meta = new EntityMeta(self::class, 'foobar', []);
        self::assertEquals('EntityMetaTest', $meta->name());
    }

    public function testGetProperty(): void
    {
        $meta = new EntityMeta(self::class, 'foobar', [
        $p1 = new EntityPropertyMeta('id', 'ID', 'int'),
        $p2 = new EntityPropertyMeta('name', 'Name', 'string'),
        ]);

        self::assertSame($p1, $meta->getProperty('id'));
        self::assertSame($p2, $meta->getProperty('name'));
    }

    public function testGetPropertyNotExist(): void
    {
        $this->expectExceptionObject(Exceptions::propertyNotFound(self::class, 'id'));

        $meta = new EntityMeta(self::class, 'foobar', [ ]);
        $meta->getProperty('id');
    }
}
