<?php

namespace Omea\GestionTelco\EvenementBundle\Exception;

/**
 * This exception replace the AccessDeniedException.
 */
class AccessDeniedException extends \RuntimeException
{
    const ACCESS_DENIED_EXCEPTION = 3000;

    /**
     * @param string            $message
     * @param int               $code
     * @param \Exception | null $previous
     */
    public function __construct($message, $code = self::ACCESS_DENIED_EXCEPTION, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
