<?php

/**
 * Test fonctionnel de service ExpeditionSimService
 *
 * Author :hlataoui
 * Email: hassine.lataoui@ext.virginmobile.fr
 */

namespace Omea\GestionMigration\MigrationBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Omea\GestionMigration\MigrationBundle\Services\ExpeditionSimService;
use Omea\Entity\MigrationFernand\Migration;
use Omea\Entity\Main\Client;
use Omea\Entity\Main\Article;
use Omea\Entity\Main\StockMsisdn;
use Omea\Entity\Main\Distributeurs;
use Omea\Entity\Main\SapHierarchieOffre;
use Omea\Entity\Main\SapNivSubOffre;

class ExpeditionSimServiceTest extends WebTestCase {

    public $mainManager;
    public $migrationFernandManager;
    public $logger;
    public $parameters;
    public $migrationRepository;
    public $articleRepository;
    public $stockMsisdnRepository;
    public $distributeursRepository;
    public $clientRepository;
    public $sapNivSubOffreRepository;
    public $sapHierarchieOffreRepository;

    public function setUp() {
        $this->mainManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $this->migrationFernandManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $this->logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();
        $this->parameters['expedition']['id_dis']=2;
        $this->parameters['expedition']['id_mag']=5;
        $this->parameters['expedition']['id_art']=3;
        $this->parameters['expedition']['trans_traite']=1;
        
        $this->migrationRepository = $this->getMockBuilder('Omea\Entity\MigrationFernand\MigrationRepository')->disableOriginalConstructor()->getMock();
        $this->articleRepository = $this->getMockBuilder('Omea\Entity\Main\ArticleRepository')->disableOriginalConstructor()->getMock();
        $this->stockMsisdnRepository = $this->getMockBuilder('Omea\Entity\Main\StockMsisdnRepository')->disableOriginalConstructor()->getMock();
        $this->distributeursRepository = $this->getMockBuilder('Omea\Entity\Main\DistributeursRepository')->disableOriginalConstructor()->getMock();
        $this->clientRepository = $this->getMockBuilder('Omea\Entity\Main\ClientRepository')->disableOriginalConstructor()->getMock();
        $this->sapNivSubOffreRepository = $this->getMockBuilder('Omea\Entity\Main\SapNivSubOffreRepository')->disableOriginalConstructor()->getMock();
        $this->sapHierarchieOffreRepository = $this->getMockBuilder('Omea\Entity\Main\SapHierarchieOffreRepository')->disableOriginalConstructor()->getMock();
    }
    
    public function mockClient() {
        $client=new Client(); 
        return array($client);
     }
    
    public function mockArticle() {
         $article=new Article(); 
         return array($article);
    }
    
    public function mockStockMsisdn() {
         $stockMsisdn=new StockMsisdn(); 
         return array($stockMsisdn);
    }
    
    public function mockDistributeurs() {
         $distributeur=new Distributeurs(); 
         return array($distributeur);
    }
    
    public function mockSapNivSubOffre() {
         $sapNivSubOffre=new SapNivSubOffre(); 
         return array($sapNivSubOffre);
    }
    
    public function mockSapHierarchieOffre() {
         $sapHierarchieOffre=new SapHierarchieOffre(); 
         return array($sapHierarchieOffre);
    }

    public function mockLignesEligibleForExpedition() {
        $migration1 = new Migration();
        $migration1->setDateDemandeExpedition(NULL);
        $migration1->setDateEnvoiAlm(NULL);
        $migration1->setDateMigrationPrevu(NULL);
        $migration1->setDatePreprovisioning(NULL);
        $migration1->setDateReceptionSms(NULL);
        $migration1->setIdClient(8315937);
        $migration1->setIdMigration(1);
        $migration1->setMsisdn(0686082661);
        $migration1->setNombreEnvoiAlm(0);
        $migration1->setRaison(NULL);
        $migration1->setStatut("BASCULE_INCOMPATIBLE");
        $migration1->setTransactionId(NULL);

        $migration2 = new Migration();
        $migration2->setDateDemandeExpedition(NULL);
        $migration2->setDateEnvoiAlm(NULL);
        $migration2->setDateMigrationPrevu(NULL);
        $migration2->setDatePreprovisioning(NULL);
        $migration2->setDateReceptionSms(NULL);
        $migration2->setIdClient(8320690);
        $migration2->setIdMigration(2);
        $migration2->setMsisdn(0770338031);
        $migration2->setNombreEnvoiAlm(0);
        $migration2->setRaison(NULL);
        $migration2->setStatut("BASCULE_INJOINABLE");
        $migration2->setTransactionId(NULL);
        return array($migration1, $migration2);
    }

    public function mockLignesNonEligibleForExpedition() {
        $migration1 = new Migration();
        $migration1->setDateDemandeExpedition(NULL);
        $migration1->setDateEnvoiAlm(NULL);
        $migration1->setDateMigrationPrevu(NULL);
        $migration1->setDatePreprovisioning(NULL);
        $migration1->setDateReceptionSms(NULL);
        $migration1->setIdClient(8315937);
        $migration1->setIdMigration(1);
        $migration1->setMsisdn(0686082661);
        $migration1->setNombreEnvoiAlm(0);
        $migration1->setRaison(NULL);
        $migration1->setStatut("BASCULE_OTA");
        $migration1->setTransactionId(NULL);

        $migration2 = new Migration();
        $migration2->setDateDemandeExpedition(NULL);
        $migration2->setDateEnvoiAlm(NULL);
        $migration2->setDateMigrationPrevu(NULL);
        $migration2->setDatePreprovisioning(NULL);
        $migration2->setDateReceptionSms(NULL);
        $migration2->setIdClient(8320690);
        $migration2->setIdMigration(2);
        $migration2->setMsisdn(0770338031);
        $migration2->setNombreEnvoiAlm(0);
        $migration2->setRaison(NULL);
        $migration2->setStatut("BASCULE_ALM");
        $migration2->setTransactionId(NULL);
        return array($migration1, $migration2);
    }

    public function testTraitementDemandeExpeditionSimSAVOk() {
        /* appel Jeu de données */
        $migrationSims = $this->mockLignesEligibleForExpedition();
        $client=$this->mockClient();
        $article=$this->mockArticle();
        $distributeur=$this->mockDistributeurs();
        $stockMsisdn=$this->mockStockMsisdn();
        $sapNivSubOffre=$this->mockSapNivSubOffre();
        $sapHierarchieOffre=$this->mockSapHierarchieOffre();
        
        $this->clientRepository->expects($this->any())->method('find')->will($this->returnValue($client));
        $this->articleRepository->expects($this->any())->method('find')->will($this->returnValue($article));
        $this->distributeursRepository->expects($this->any())->method('findOneBy')->will($this->returnValue($distributeur));
        $this->stockMsisdnRepository->expects($this->any())->method('findOneBy')->will($this->returnValue($stockMsisdn));
        $this->sapNivSubOffreRepository->expects($this->any())->method('find')->will($this->returnValue($sapNivSubOffre));
        $this->sapHierarchieOffreRepository->expects($this->any())->method('find')->will($this->returnValue($sapHierarchieOffre));

        /* initialisation de service */
        $service = new ExpeditionSimService(
                $this->mainManager, $this->migrationFernandManager, $this->logger, $this->parameters
        );
        /* appel function de traitement demande expedition */
        $service->traitementDemandeExpeditionSimSAV($migrationSims);

        /* vérifie que les champs sont mis à jour dans la table MIGRATION */
        foreach ($migrationSims as $migration) {
            $this->assertInstanceOf('\DateTime', $migration->getDateDemandeExpedition());
            $this->assertEquals('BASCULE_CHANGEMENT_SIM_DEMANDE', $migration->getStatut());
            $this->assertNotEmpty($migration->getTransactionId());
        }
    }

    public function testTraitementDemandeExpeditionSimSAVKo() {
        /* appel Jeu de données */
        $migrationSims = $this->mockLignesNonEligibleForExpedition();
        $client=$this->mockClient();
        $article=$this->mockArticle();
        $distributeur=$this->mockDistributeurs();
        $stockMsisdn=$this->mockStockMsisdn();
        $sapNivSubOffre=$this->mockSapNivSubOffre();
        $sapHierarchieOffre=$this->mockSapHierarchieOffre();        
        
        $this->clientRepository->expects($this->any())->method('find')->will($this->returnValue($client));
        $this->articleRepository->expects($this->any())->method('find')->will($this->returnValue($article));
        $this->distributeursRepository->expects($this->any())->method('findOneBy')->will($this->returnValue($distributeur));
        $this->stockMsisdnRepository->expects($this->any())->method('findOneBy')->will($this->returnValue($stockMsisdn));
        $this->sapNivSubOffreRepository->expects($this->any())->method('find')->will($this->returnValue($sapNivSubOffre));
        $this->sapHierarchieOffreRepository->expects($this->any())->method('find')->will($this->returnValue($sapHierarchieOffre));

        /* initialisation de service */
        $service = new ExpeditionSimService(
                $this->mainManager, $this->migrationFernandManager, $this->logger, $this->parameters
        );
        /* appel function de traitement demande expedition */
        $service->traitementDemandeExpeditionSimSAV($migrationSims);

        /* vérifie que les champs ne sont pas mis à jour dans la table MIGRATION */
        foreach ($migrationSims as $migration) {
            $this->assertNull($migration->getDateDemandeExpedition());
            $this->assertNull($migration->getTransactionId());
        }
    }

}
