<?php

namespace Omea\GestionTelco\EvenementBundle\Services;

use Omea\GestionTelco\EvenementBundle\Exception\InvalidArgumentException;
use Omea\GestionTelco\EvenementBundle\Exception\NotFoundException;
use Omea\GestionTelco\EvenementBundle\Types\SaveEvenementRequest;
use Omea\GestionTelco\EvenementBundle\Types\SaveEvenementResponse;
use Omea\GestionTelco\EvenementBundle\Types\BaseResponse;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Omea\Entity\GestionEvenements\Evenement;
use Omea\Entity\GestionEvenements\EvenementRepository;

class EvenementService
{

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManager
     */
    protected $gestionEvenementsManagerManager;


    /**
     * @param ValidatorInterface $validator
     * @param LoggerInterface   $logger
     * @param RegistryInterface $doctrine
     */
    public function __construct(ValidatorInterface $validator,
                                LoggerInterface $logger,
                                EntityManager $gestionEvenementsManagerManager)
    {
        $this->validator = $validator;
        $this->logger = $logger;
        $this->gestionEvenementsManagerManager = $gestionEvenementsManagerManager;
    }

    /**
     * @param \Omea\GestionTelco\EvenementBundle\Types\SaveEvenementRequest $request
     *
     * @return \Omea\GestionTelco\EvenementBundle\Types\SaveEvenementResponse $response
     */
    public function saveEvenement(SaveEvenementRequest $request)
    {
        $this->logger->info(sprintf('Save Evenement start with request: %s', print_r($request, true)));
        try {
            $this->validate($request);
            $response = $this->saveAction($request);
            $logLvl = 'info';
        } catch (NotFoundException $e) {
            $logLvl = 'warning';
            $response = new SaveEvenementResponse($e->getCode(), $e->getMessage(),0);
        } catch (InvalidArgumentException $e) {
            $logLvl = 'warning';
            $response = new SaveEvenementResponse($e->getCode(), $e->getMessage(),0);
        } catch (\Exception $e) {
            $logLvl = 'error';
            $response = new SaveEvenementResponse($e->getCode(), $e->getMessage(),0);
        }

        $this->logger->$logLvl(sprintf('Save Evenement end with response: %s', print_r($response, true)));

        return $response;
    }

    /**
     * Method validating a request using the validator component.
     *
     * @param $request
     * @throw InvalidArgumentException
     */
    private function validate($request)
    {
        $this->logger->debug(sprintf('Validating %s', get_class($request)));
        $errorList = $this->validator->validate($request);

        if (count($errorList) > 0) {
            $errorMessage = '';
            foreach ($errorList as $err) {
                $errorMessage .= $err->getMessage().' - ';
            }
            $this->logger->debug(sprintf('Error during validation : %s', $errorMessage));
            throw new InvalidArgumentException($errorMessage);
        }
    }

    private function saveAction(SaveEvenementRequest $request)
    {
        $this->logger->debug('save evenement begin');

        if ($request->msisdn && $request->code && $request->type) {
          
                $evenement = new Evenement();
                $msisdn = $request->msisdn;
                $code = $request->code;
                $type = $request->type;
                $evenement->setMsisdn($msisdn);
                $evenement->setCode($code);
                $evenement->setType($type);
                $evenement->setErreur(0);
                $evenement->setDateAppel(new \Datetime('now'));
                $evenement->setDateTraitement(null);

                $this->gestionEvenementsManagerManager->persist($evenement);
                $this->gestionEvenementsManagerManager->flush();
                 return new SaveEvenementResponse('0','Inserted OK',1);
            
        }else{
                 return new SaveEvenementResponse(InvalidArgumentException::INVALID_ARGUMENT_EXCEPTION, 'Inserted KO',0);
        }
    }


}
