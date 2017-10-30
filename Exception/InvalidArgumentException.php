<?php

namespace Omea\GestionTelco\EvenementBundle\Exception;

use InvalidArgumentException as PHPInvalidArgumentException;

/**
 * This exception replace the InvalidArgumentException.
 */
class InvalidArgumentException extends PHPInvalidArgumentException
{
    const INVALID_ARGUMENT_EXCEPTION = 4000;

    /**
     * @param string            $message
     * @param int               $code
     * @param \Exception | null $previous
     */
    public function __construct(
        $message,
        $code = self::INVALID_ARGUMENT_EXCEPTION,
        $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
