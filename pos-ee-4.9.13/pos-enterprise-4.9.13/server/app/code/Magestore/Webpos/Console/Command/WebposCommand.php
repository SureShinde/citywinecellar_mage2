<?php

namespace Magestore\Webpos\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magestore\Webpos\Api\Console\WebposDeployInterface;

class WebposCommand extends Command {

    /**
     * @var WebposDeployInterface
     */
    protected $webposDeployInterface;

    /**
     * WebposCommand constructor.
     * @param WebposDeployInterface $webposDeployInterface
     */
    public function __construct(
        \Magestore\Webpos\Api\Console\WebposDeployInterface $webposDeployInterface
    )
    {
        parent::__construct();
        $this->webposDeployInterface = $webposDeployInterface;
    }

    protected function configure()
    {
        $this->setName('webpos:deploy');
        $this->setDescription('Deploy the pos app');
        //$this->addArgument('webpos_deploy_command', InputArgument::REQUIRED, 'Deploy Command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            //$deployCommand =  $input->getArgument('webpos_deploy_command');
            $deployCommand =  '';
            $this->webposDeployInterface->webposDeploy($deployCommand, $output);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
        }
    }

}