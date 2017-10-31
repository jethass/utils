<?php

/**
 * Created by PhpStorm.
 * User: rserale
 * Date: 22/06/15
 * Time: 17:25.
 */
namespace Omea\GestionTelco\EvenementBundle\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Omea\Entity\GestionEvenements\ActeDefinition;
use Omea\Entity\GestionEvenements\Service;
use Omea\Entity\GestionEvenements\Acte;

class TrameType extends Type
{
    const TRAMETYPE = 'trame_type';

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array                                     $fieldDeclaration The field declaration.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform         The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return 'trame_type';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $arr = array_map(function ($json) {
            $service = new Service();
            $service->setNom($json->name);

            $acte = new Acte();
            $acte->setService($service);
            $acte->setOptions($json->options);

            $acteDef = new ActeDefinition();

            $acteDef->setActe($acte);

            return $acteDef;
        }, json_decode($value));
        //convertir données JSON en array de ActeDefinition
        return ($arr);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $arr = array();
        
        //sérialiser array de ActeDefinition en json
        foreach ($value as $elem) {
            $arr[] = array('name' => $elem->getName(), 'options' => $elem->getOptions());
        }
        
        return (json_encode($arr));
    }
}
