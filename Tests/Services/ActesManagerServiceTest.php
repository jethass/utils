<?php
/**
 * Description of ActesManagerServiceTest
 *
 * @author hlataoui
 */
namespace Omea\GestionTelco\EvenementBundle\Tests\Services;

use Omea\Entity\GestionEvenements\Evenement;
use Omea\Entity\GestionEvenements\GestionEvenementErreur;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Omea\GestionTelco\EvenementBundle\ActeManager\ActesManager;
use Omea\GestionTelco\EvenementBundle\Services\ActesManagerService;
use Omea\Entity\Main\StockMsisdn;

class ActesManagerServiceTest extends WebTestCase 
{
    public $evenementRepository;
    public $gestionEvenementErreurRepository;
    public $logger;

    public function setUp()
    {
            $this->evenementRepository = $this->getMockBuilder('Omea\Entity\GestionEvenements\EvenementRepository')->disableOriginalConstructor()->getMock();
            $this->gestionEvenementErreurRepository = $this->getMockBuilder('Omea\Entity\GestionEvenements\GestionEvenementErreurRepository')->disableOriginalConstructor()->getMock();
            $this->logger=$this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();
    }
        
    public function MockEvenements()
    {
            $evenement1 = new Evenement();
            $evenement1->setCode('Alerte_HIS');
            $evenement1->setDateAppel(new \Datetime('now'));
            $evenement1->setDateTraitement(null);
            $evenement1->setMsisdn('601088866');
            $evenement1->setType('NOTIFICATION');
            $evenement1->setErreurRaison('');
            $evenement1->setErreur(0);

            $evenement2 = new Evenement();
            $evenement2->setCode('Alerte_OCR');
            $evenement2->setDateAppel(new \Datetime('now'));
            $evenement2->setDateTraitement(null);
            $evenement2->setMsisdn('601698866');
            $evenement2->setType('NOTIFICATION');
            $evenement2->setErreurRaison('');
            $evenement2->setErreur(0);

            return array($evenement1, $evenement2);
     
    }
    
    public function MockGestionEvenementsErreur()
    {
            $gestionEvenementErreur1 = new GestionEvenementErreur();
            $gestionEvenementErreur1->setActeKo(3);
            $gestionEvenementErreur1->setDateErreur(new \Datetime('now'));
            $gestionEvenementErreur1->setErreurMessage("Le MSISDN 214253687 n'existe pas");
            $gestionEvenementErreur1->setEtat(0);
            $gestionEvenementErreur1->setEvenement(4);
            $gestionEvenementErreur1->setTrame('[{"name":"bridage","options":"idOption=33&idOptionGroupe=null"}]');

            $gestionEvenementErreur2 = new GestionEvenementErreur();
            $gestionEvenementErreur2->setActeKo(3);
            $gestionEvenementErreur2->setDateErreur(new \Datetime('now'));
            $gestionEvenementErreur2->setErreurMessage("Le MSISDN 214253687 n'existe pas");
            $gestionEvenementErreur2->setEtat(0);
            $gestionEvenementErreur2->setEvenement(5);
            $gestionEvenementErreur2->setTrame('[{"name":"bridage","options":"idOption=33&idOptionGroupe=null"}]');
            return array($gestionEvenementErreur1, $gestionEvenementErreur2);
    }
    
    
    /* test la partie1 handleEvenements (si le traitement des evenements passe et ne catch pas d'erreurs) */
    public function testHandleEvenementsOk()
    {
            /* Prépare tous les mock pour l'appel de service ActesManagerService*/
            $mockEvenements = $this->MockEvenements();
            $this->evenementRepository->expects($this->once())->method('findBy')->will($this->returnValue($mockEvenements));

            /* Mock StockMsisdnRepository */
            $mockStockMsisdnRepository = $this->getMockBuilder('Omea\Entity\Main\StockMsisdnRepository')->disableOriginalConstructor()->getMock();
            /* Mock ActeManager */
            $mockActeManager = $this->getMockBuilder('Omea\GestionTelco\EvenementBundle\ActeManager\ActesManager')->disableOriginalConstructor()->getMock();
            /* Mock EntityManagerInterface */
            $gestionEvenementManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

            /* instancation de  ActesManagerService*/
            $service = new ActesManagerService(
                $this->logger,
                $gestionEvenementManager,
                $mockActeManager,
                $mockStockMsisdnRepository,
                $this->evenementRepository,
                $this->gestionEvenementErreurRepository    
            );

            /* attribue un client pour $stockmsisdn */
            $stockMsisdn = new StockMsisdn();
            $stockMsisdn->setIdClient(42);
            /* StockMsisdnRepository Mock va accepter plusieur fois la méthode find qui va retourné un $stockMsisdn qui contient un IdClient=42 tjs  */
            $mockStockMsisdnRepository->expects($this->any())->method('find')->will($this->returnValue($stockMsisdn));

            /* Appel la function handle d'ActeManager 2 fois (qui est égal au nombre de mock des evenements)*/
            $mockActeManager->expects($this->exactly(2))->method('handle');

            /* Appel de la function handleEvenements de ActesManagerService */
            $service->handleEvenements();

            /* vérifie que DateTraitement est mis à jour par une \DateTime */
            foreach ($mockEvenements as $evenement) {
                $this->assertInstanceOf('\DateTime', $evenement->getDateTraitement());
            }
    }
    
    /* test la partie2 handleEvenements (si le traitement des evenements catch une erreur) */
    public function testHandleEvenementsKo()
    {
            /* Prépare tous les mock pour l'appel de service ActesManagerService*/
           $mockEvenements = $this->MockEvenements();

           $this->evenementRepository->expects($this->once())->method('findBy')->will($this->returnValue($mockEvenements));

           /* Mock StockMsisdnRepository */
           $mockStockMsisdnRepository = $this->getMockBuilder('Omea\Entity\Main\StockMsisdnRepository')->disableOriginalConstructor()->getMock();
           /* Mock EntityManagerInterface */
           $gestionEvenementManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
             /* Mock ActeManager */
           $mockActeManager = $this->getMockBuilder('Omea\GestionTelco\EvenementBundle\ActeManager\ActesManager')->disableOriginalConstructor()->getMock();

           /* instancation de  ActesManagerService*/
           $service = new ActesManagerService(
               $this->logger,
               $gestionEvenementManager,
               $mockActeManager,
               $mockStockMsisdnRepository,
               $this->evenementRepository,
               $this->gestionEvenementErreurRepository
           );

           /*initialisation d'objet $stockMsisdn null pour lui faire passer au StockMsisdnRepository et pour que le traitement de handle retourne une erreur*/
           $stockMsisdn = null;
           /* StockMsisdnRepository Mock va accepter plusieur fois la méthode find qui va retourné un $stockMsisdn null  */
           $mockStockMsisdnRepository->expects($this->any())->method('find')->will($this->returnValue($stockMsisdn));

           /*vérifie que l'ActeManager n'appelle pas la function handle car on est dans la deusième partie de fonction ou on catch un erreur */
           $mockActeManager->expects($this->never())->method('handle');
           /*Erreur raison de plantge retourner par l'acte manger -pour le test de remplissage $gestionEvenementErreur- */
           $mockActeManager->expects($this->any())->method('getErreurRaison')->will($this->returnValue('inconnu'));

           /* Appel de la function handleEvenements de ActesManagerService */
           $service->handleEvenements();

           foreach ($mockEvenements as $evenement) {
               /* vérifie que DateTraitement reste Null */
               $this->assertNull($evenement->getDateTraitement());

               /*vérifie que le champ erreur de l'evenement devient égale à 1 */
               $this->assertEquals(1, $evenement->getErreur());

               /*vérifie que le champ erreur raison est bien remplie */
               $this->assertEquals('inconnu', $evenement->getErreurRaison());
           }
  
    }
    
    
    /* test la partie1 de RattrapageEvenements (si le rattrapage de traitement des evenements passe et ne catch pas d'erreurs) */
    public function testRattrapageEvenementsOk()
    {
        
            /* Prépare tous les mock pour l'appel de service ActesManagerService*/
            $mockGestionEvenementsErreur = $this->MockGestionEvenementsErreur();
            $this->gestionEvenementErreurRepository->expects($this->once())->method('findBy')->will($this->returnValue($mockGestionEvenementsErreur));

            /* Mock StockMsisdnRepository */
            $mockStockMsisdnRepository = $this->getMockBuilder('Omea\Entity\Main\StockMsisdnRepository')->disableOriginalConstructor()->getMock();
            /* Mock ActeManager */
            $mockActeManager = $this->getMockBuilder('Omea\GestionTelco\EvenementBundle\ActeManager\ActesManager')->disableOriginalConstructor()->getMock();
             /* Mock EntityManagerInterface */
            $gestionEvenementManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

            /* instancation de  ActesManagerService*/
            $service = new ActesManagerService(
                $this->logger,
                $gestionEvenementManager,
                $mockActeManager,
                $mockStockMsisdnRepository,
                $this->evenementRepository,
                $this->gestionEvenementErreurRepository    
            );

            /* attache cet evenement pour chaque $gestionEvenementErreur */
            $evenement = new Evenement();
            $evenement->setCode('Alerte_HIS');
            $evenement->setDateAppel(new \Datetime('now'));
            $evenement->setDateTraitement(null);
            $evenement->setMsisdn('601088866');
            $evenement->setType('NOTIFICATION');

            foreach ($mockGestionEvenementsErreur as $gestionEvenementErreur) {
                $gestionEvenementErreur->setEvenement($evenement);
            }

            /* attribue un client pour $stockmsisdn */
            $stockMsisdn = new StockMsisdn();
            $stockMsisdn->setIdClient(42);
            /* StockMsisdnRepository Mock va accepter plusieur fois la méthode find qui va retourné un $stockMsisdn qui contient un IdClient=42 tjs  */
            $mockStockMsisdnRepository->expects($this->any())->method('find')->will($this->returnValue($stockMsisdn));

             /* Appel la function handle d'ActeManager 2 fois (qui est égal au nombre de mock des evenements)*/
            $mockActeManager->expects($this->exactly(2))->method('handle');

            /* Appel de la function rattrapageEvenements de ActesManagerService */
            $service->rattrapageEvenements();

            foreach ($mockGestionEvenementsErreur as $gestionEvenementErreur) {
                 $evenement = $gestionEvenementErreur->getEvenement();

                 /* vérifie que DateTraitement de traitement de l'evenement est mis à jour par une \DateTime */
                 $this->assertInstanceOf('\DateTime', $evenement->getDateTraitement());

                 /* vérifie que Etat de GestionEvenementErreur devient 1 "ETAT_TRAITE" */
                 $this->assertEquals(GestionEvenementErreur::ETAT_TRAITE, $gestionEvenementErreur->getEtat());
            }
    }
    
     /* test la partie2 RattrapageEvenements (si le rattrapage de traitement des evenements catch une erreur) */
    public function testRattrapageEvenementsKo()
    {
            /* Prépare tous les mock pour l'appel de service ActesManagerService*/
            $mockGestionEvenementsErreur = $this->MockGestionEvenementsErreur();
            $this->gestionEvenementErreurRepository->expects($this->once())->method('findBy')->will($this->returnValue($mockGestionEvenementsErreur));

            /* Mock StockMsisdnRepository */
            $mockStockMsisdnRepository = $this->getMockBuilder('Omea\Entity\Main\StockMsisdnRepository')->disableOriginalConstructor()->getMock();
            /* Mock ActeManager */
            $mockActeManager = $this->getMockBuilder('Omea\GestionTelco\EvenementBundle\ActeManager\ActesManager')->disableOriginalConstructor()->getMock();
            /* Mock EntityManagerInterface */
            $gestionEvenementManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

            /* instancation de  ActesManagerService*/
            $service = new ActesManagerService(
                $this->logger,
                $gestionEvenementManager,
                $mockActeManager,
                $mockStockMsisdnRepository,
                $this->evenementRepository,
                $this->gestionEvenementErreurRepository
            );

             /* attache cet evenement pour chaque $gestionEvenementErreur */
            $evenement = new Evenement();
            $evenement->setCode('Alerte_HIS');
            $evenement->setDateAppel(new \Datetime('now'));
            $evenement->setDateTraitement(null);
            $evenement->setMsisdn('601088866');
            $evenement->setType('NOTIFICATION');
            $evenement->setErreurRaison('');
            $evenement->setErreur(0);

            foreach ($mockGestionEvenementsErreur as $gestionEvenementErreur) {
                $gestionEvenementErreur->setEvenement($evenement);
            }

            /*initialisation d'objet $stockMsisdn null pour lui faire passer au StockMsisdnRepository et pour que le traitement de handle retourne une erreur*/
            $stockMsisdn = null;
            /* StockMsisdnRepository Mock va accepter plusieur fois la méthode find qui va retourné un $stockMsisdn null  */
            $mockStockMsisdnRepository->expects($this->any())->method('find')->will($this->returnValue($stockMsisdn));

            /*vérifie que l'ActeManager n'appelle pas la function handle car on est dans la deusième partie de fonction ou on catch un erreur */
            $mockActeManager->expects($this->never())->method('handle');

            /* Appel de la function handleEvenements de ActesManagerService */
            $service->rattrapageEvenements();

            foreach ($mockGestionEvenementsErreur as $gestionEvenementErreur) {
                 $evenement = $gestionEvenementErreur->getEvenement();

                 /* vérifie que DateTraitement de l'ancien GestionEvenementErreur reste null */
                 $this->assertNull($evenement->getDateTraitement());

                 /* vérifie que l'Etat de l'ancien GestionEvenementErreur devient -1 "ETAT_ABANDON" */
                 $this->assertEquals(GestionEvenementErreur::ETAT_ABANDON, $gestionEvenementErreur->getEtat());
            }
    }

}
