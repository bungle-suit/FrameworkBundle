<?php
declare(strict_types=1);

namespace Bungle\FrameworkBundle\Tests\StateMachine\EventListener;

use Bungle\FrameworkBundle\Meta\HighPrefix;
use Bungle\FrameworkBundle\Meta\SimpleEntityLocator;
use Bungle\FrameworkBundle\StateMachine\EventListener\TransitionEventListener;
use Bungle\FrameworkBundle\Tests\StateMachine\Entity\Order;

final class TransitionEventListenerTest extends TestBase
{
    public function setUp(): void
    {
        parent::setUp();

        $listener = new TransitionEventListener(
            new HighPrefix(
                new SimpleEntityLocator([Order::class])
            )
        );
        $this->dispatcher->addListener('workflow.transition', $listener);
    }

    public function testGetSTTClass(): void
    {
        $f = TransitionEventListener::class.'::getSTTClass';

        self::assertEquals('STT\FooSTT', $f('Entity\Foo'));
        self::assertEquals('Order\STT\BarSTT', $f('Order\Entity\Bar'));
    }

    public function testInvoke(): void
    {
        $this->sm->apply($this->ord, 'save');
        self::assertEquals('foo', $this->ord->code);
        self::assertEquals('saved', $this->ord->state);
    }

    public function testInvokeWithContext(): void
    {
        $this->ord->state = 'saved';
        $this->sm->apply($this->ord, 'update');
        self::assertEquals('update', $this->ord->code);
        self::assertEquals('saved', $this->ord->state);
    }

    public function testIgnoreStepsNotConfigured(): void
    {
        self::expectWarning();
        $this->ord->state = 'saved';
        $this->sm->apply($this->ord, 'print');
        self::assertEquals('saved', $this->ord->state);
    }
}
