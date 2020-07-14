<?php

namespace Magestore\PosSampleData\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magestore\Webpos\Api\Console\WebposDeployInterface;

/**
 * Class GenerateOrder
 *
 * @package Magestore\PosSampleData\Console\Command
 */
class GenerateOrder extends Command
{
    /**
     * @var WebposDeployInterface
     */
    protected $webposDeployInterface;
    /**
     * @var \Magestore\PosSampleData\Model\FixtureGenerator\Order\OrderDataGenerator
     */
    protected $orderDataGenerator;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * GenerateOrder constructor.
     *
     * @param WebposDeployInterface $webposDeployInterface
     * @param \Magestore\PosSampleData\Model\FixtureGenerator\Order\OrderDataGenerator $orderDataGenerator
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magestore\Webpos\Api\Console\WebposDeployInterface $webposDeployInterface,
        \Magestore\PosSampleData\Model\FixtureGenerator\Order\OrderDataGenerator $orderDataGenerator,
        \Magento\Framework\App\State $appState
    ) {
        parent::__construct();
        $this->webposDeployInterface = $webposDeployInterface;
        $this->orderDataGenerator = $orderDataGenerator;
        $this->_appState = $appState;
    }

    /**
     * Command configure
     */
    protected function configure()
    {
        $this->setName('webpos:generate:order');
        $this->setDescription('Generate sample order data');
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->_appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        } catch (\Exception $e) {
            $this->_appState->getAreaCode();
        }

        try {
            $this->orderDataGenerator->execute();
            $output->writeln(sprintf('Generate order successfully!'));
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
        }
    }
}
