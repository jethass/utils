<?php

namespace Omea\GestionTelco\EvenementBundle\Types;

abstract class AbstractRequestType
{
    public function __toString()
    {
        return print_r($this, true);
    }
}
