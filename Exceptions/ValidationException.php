<?php
declare(strict_types=1);

namespace Bungle\FrameworkBundle\Exceptions;

/**
 * ValidationException raised because of data fails validate,
 * such as has wrong property value.
 */
class ValidationException extends \RuntimeException
{
}
