<?php
namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Product;

class SearchIndexer
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        // only act on some "Product" entity
        if (!$object instanceof Product) {
            return;
        }

        $objectManager = $args->getObjectManager();
        // ... do something with the Product
    }
}