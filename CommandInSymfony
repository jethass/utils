<?php
namespace AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('gestiontelco:import')
            ->setDescription('Handle an import of the requested application')
            ->addArgument('application', InputArgument::REQUIRED, 'Which application do you want to import?')
            //->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Choose the number ot item to proceed (default 1)')
        ;
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
                 $output->writeln("Debut du traitement d'import "); 
                 $application = $input->getArgument('application');
                // $limit = $input->getOption('limit');
        
                 $importService = $this->getContainer()->get('nom_de_service');
                 $importService->import($application);
           } catch(\Exception $e) {
                 $output->writeln("Erreur : ".$e->getMessage());
         }
    }
}
