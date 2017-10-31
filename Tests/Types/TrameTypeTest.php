<?php

namespace Omea\GestionTelco\EvenementBundle\Tests\Types;

use Doctrine\DBAL\Types\Type;
use Omea\Entity\GestionEvenements\ActeDefinition;
use Omea\Entity\GestionEvenements\Service;
use Omea\GestionTelco\EvenementBundle\Types\TrameType;
use Omea\Entity\GestionEvenements\Acte;

class TrameTypeTest extends \PHPUnit_Framework_TestCase
{
    private $platformMock;
    private $trameType;
    private $acteDefinitions;
    /**
     * Enregistre le type doctrine.
     *
     * @beforeClass
     */
    public static function setUpType()
    {
        Type::addType('trametype', 'Omea\GestionTelco\EvenementBundle\Types\TrameType');
    }

    public function setUp()
    {
        $this->platformMock = $this->getMockBuilder('\Doctrine\DBAL\Platforms\AbstractPlatform')->getMockForAbstractClass();
        $this->trameType = Type::getType('trametype');

        $service1 = new Service();
        $service2 = new Service();
        $service1->setNom('millenium_falcon');
        $service2->setNom('enterprise');

        $acte1 = new Acte();
        $acte2 = new Acte();
        $acte1->setService($service1);
        $acte2->setService($service2);
        $acte1->setOptions('pilot=han_solo&copilot=chewbacca');
        $acte2->setOptions('pilot=kirk&copilot=spock');

        $acteDef1 = new ActeDefinition();
        $acteDef2 = new ActeDefinition();

        $acteDef1->setActe($acte1);
        $acteDef2->setActe($acte2);

        $this->acteDefinitions = [$acteDef1,$acteDef2];
    }

    public function testToSQLValue()
    {
        $expectedJson = '[{"name":"millenium_falcon","options":"pilot=han_solo&copilot=chewbacca"},{"name":"enterprise","options":"pilot=kirk&copilot=spock"}]';

        $this->assertEquals(
            $expectedJson,
            $this->trameType->convertToDatabaseValue($this->acteDefinitions, $this->platformMock)
        );
    }

    public function testToPHPValue()
    {
        $jsonDefinition = '[{"name":"millenium_falcon","options":"pilot=han_solo&copilot=chewbacca"},{"name":"enterprise","options":"pilot=kirk&copilot=spock"}]';


        $this->assertEquals(
            $this->acteDefinitions,
            $this->trameType->convertToPHPValue($jsonDefinition, $this->platformMock)
        );
    }
}
