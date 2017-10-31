<?php

namespace Omea\GestionTelco\EvenementBundle\Tests\Validator\Constraint;

use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Validation;
use Omea\GestionTelco\EvenementBundle\Validator\Constraint\IsValidCODE;
use Omea\GestionTelco\EvenementBundle\Validator\Constraint\IsValidCODEValidator;

class IsValidCODEValidatorTest extends AbstractConstraintValidatorTest
{
    protected function getApiVersion()
    {
        return Validation::API_VERSION_2_5;
    }

    protected function createValidator()
    {
        return new IsValidCODEValidator();
    }

    /**
     * @dataProvider getValidElements
     */
    public function testValidateOk($value)
    {
        $constraint = new IsValidCODE();
        $this->validator->validate($value, $constraint);
        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidElements
     */
    public function testValidateFail($value)
    {
        $constraint = new IsValidCODE();
        $this->validator->validate($value, $constraint);
        $this->buildViolation('The CODE must be composed by 10 caracters or less')
            ->setParameter('%string%', $value)
            ->assertRaised();
    }

    public function getValidElements()
    {
        return array(
            array('Alerte_OCR'),
            array('Alerte_USE'),
            array('Alerte_PYS'),
        );
    }

    public function getInvalidElements()
    {
        return array(
            array('Alerte_gffhf'),
            array('Alerte_gqsdsffhf'),
        );
    }
}
