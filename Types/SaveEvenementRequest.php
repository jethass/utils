<?php

namespace Omea\GestionTelco\EvenementBundle\Types;

class SaveEvenementRequest extends AbstractRequestType
{
    /**
     * MSISDN.
     *
     * @var string
     */
    public $msisdn;

    /**
     * Code Evenement.
     *
     * @var string
     */
    public $code;

    /**
     * Type Evenement.
     *
     * @var string
     */
    public $type;
}
