<?php

namespace Omea\GestionTelco\EvenementBundle\Exception;

/**
 * This exception allow you to signal technical issues.
 */
class TechnicalException extends \RuntimeException
{
    const TECHNICAL_EXCEPTION = 1000;
    const NUMABO_MSISDN = 1001;

    /**
     * @param string            $message
     * @param int               $code
     * @param \Exception | null $previous
     */
    public function __construct($message, $code = self::TECHNICAL_EXCEPTION, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
