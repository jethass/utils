<?php

namespace Omea\GestionTelco\EvenementBundle\Tests\Validator\Constraint;

use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Validation;
use Omea\GestionTelco\EvenementBundle\Validator\Constraint\IsValidMSISDN;
use Omea\GestionTelco\EvenementBundle\Validator\Constraint\IsValidMSISDNValidator;

class IsValidMSISDNValidatorTest extends AbstractConstraintValidatorTest
{
    protected function getApiVersion()
    {
        return Validation::API_VERSION_2_5;
    }

    protected function createValidator()
    {
        return new IsValidMSISDNValidator();
    }

    /**
     * @dataProvider getValidElements
     */
    public function testValidateOk($value)
    {
        $constraint = new IsValidMSISDN();
        $this->validator->validate($value, $constraint);
        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidElements
     */
    public function testValidateFail($value)
    {
        $constraint = new IsValidMSISDN();
        $this->validator->validate($value, $constraint);
        $this->buildViolation('The MSISDN must be composed by 10 digits')
            ->setParameter('%string%', $value)
            ->assertRaised();
    }

    public function getValidElements()
    {
        return array(
            array('0000000000'),
            array('0123456789'),
            array('1111111111'),
            array('9999999999'),
        );
    }

    public function getInvalidElements()
    {
        return array(
            array('000000000'),
            array('fail'),
            array('------'),
            array('99999999999999999'),
        );
    }
}
