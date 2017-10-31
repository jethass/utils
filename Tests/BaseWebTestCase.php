<?php

namespace Omea\GestionTelco\EvenementBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;

class BaseWebTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Default EntityManager (provisioning_pates).
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Main EntityManager (main_vm).
     *
     * @var EntityManager
     */
    protected $emMain;

    /**
     * SetUp client + the entities managers.
     */
    public function setUp()
    {
        $this->client = parent::createClient();

        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->em->getConnection()->beginTransaction();

        $this->emMain = $this->client->getContainer()->get('doctrine.orm.main_entity_manager');
        $this->emMain->getConnection()->beginTransaction();
    }

    /**
     * Rollback the test database modifications.
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->getConnection()->rollback();
        $this->emMain->getConnection()->rollback();
    }

    public function getMockLogger()
    {
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->disableOriginalConstructor()->getMock();

        return $logger;
    }

    protected function getMockRepository($mockObject)
    {
        $mockRepository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $mockRepository->expects($this->any())
            ->method('find')
            ->will($this->returnValue($mockObject));
        $mockRepository->expects($this->any())
            ->method('findOneBy')
            ->will($this->returnValue($mockObject));
        $mockRepository->expects($this->any())
            ->method('findBy')
            ->will($this->returnValue($mockObject));

        return $mockRepository;
    }

    protected function getMockEntityManager($mockRepository)
    {
        $mockEntityManager = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $mockEntityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($mockRepository));

        return $mockEntityManager;
    }

    public function getMockDoctrine($mockEntityManager)
    {
        $doctrineMock = $this
            ->getMockBuilder('Symfony\Bridge\Doctrine\RegistryInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $doctrineMock->method('getManager')
            ->will($this->returnValue($mockEntityManager));

        return $doctrineMock;
    }
}
