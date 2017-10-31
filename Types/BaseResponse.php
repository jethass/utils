<?php

namespace Omea\GestionTelco\EvenementBundle\Types;

class BaseResponse
{
    /**
     * @var int
     */
    public $responseCode = '';

    /**
     * @var string
     */
    public $message = '';

    /**
     * @param string $responseCode
     * @param string $message
     */
    public function __construct($responseCode, $message)
    {
        $this->responseCode = $responseCode;
        $this->message = $message;
    }

    public function __toString()
    {
        return print_r($this, true);
    }
}
