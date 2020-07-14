<?php

namespace Magestore\PosSampleData\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magestore\PosSampleData\Model\FixtureGenerator\Customer\GenerateDataSetForStability;

/**
 * Class GenerateCustomer
 *
 * @package Magestore\PosSampleData\Console\Command
 */
class GenerateCustomer extends Command
{
    /**
     * @var GenerateDataSetForStability
     */
    protected $customerGenerateData;
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * GenerateCustomer constructor.
     *
     * @param GenerateDataSetForStability $generateDataSetForStability
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        GenerateDataSetForStability $generateDataSetForStability,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct();
        $this->customerGenerateData = $generateDataSetForStability;
        $this->_appState = $appState;
        $this->registry = $registry;
    }

    /**
     * Command configure
     */
    protected function configure()
    {
        $this->setName('webpos:generate:customer');
        $this->setDescription('Generate sample customer data');
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
            $this->customerGenerateData->execute();
            $output->writeln(sprintf('Generate customer successfully!'));
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
        }
    }
}
