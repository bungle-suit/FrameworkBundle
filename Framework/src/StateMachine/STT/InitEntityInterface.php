<?php
declare(strict_types=1);

namespace Bungle\Framework\StateMachine\STT;

/**
 * If STT implement this interface, STT->createNew()
 * use calling the init steps to init the newly created
 * entity object.
 */
interface InitEntityInterface
{
    /**
     * Returns callbacks to init entity object, accept one
     * argument the entity object.
     *
     * @return callback[]
     */
    function initSteps(): array;
}
