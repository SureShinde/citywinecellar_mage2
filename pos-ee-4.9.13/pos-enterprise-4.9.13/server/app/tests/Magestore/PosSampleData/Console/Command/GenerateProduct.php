<?php

namespace Magestore\PosSampleData\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magestore\Webpos\Api\Console\WebposDeployInterface;

/**
 * Class GenerateProduct
 *
 * @package Magestore\PosSampleData\Console\Command
 */
class GenerateProduct extends Command
{
    /**
     * @var WebposDeployInterface
     */
    protected $webposDeployInterface;
    /**
     * @var \Magestore\PosSampleData\Model\FixtureGenerator\Product\ProductGenerateData
     */
    protected $productGenerateData;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * GenerateProduct constructor.
     *
     * @param WebposDeployInterface $webposDeployInterface
     * @param \Magestore\PosSampleData\Model\FixtureGenerator\Product\ProductGenerateData $productGenerateData
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magestore\Webpos\Api\Console\WebposDeployInterface $webposDeployInterface,
        \Magestore\PosSampleData\Model\FixtureGenerator\Product\ProductGenerateData $productGenerateData,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct();
        $this->webposDeployInterface = $webposDeployInterface;
        $this->productGenerateData = $productGenerateData;
        $this->_appState = $appState;
        $this->registry = $registry;
    }

    /**
     * Command configure
     */
    protected function configure()
    {
        $this->setName('webpos:generate:product');
        $this->setDescription('Generate sample product data');
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

        $this->registry->unregister('isSecureArea');
        $this->registry->register('isSecureArea', true);

        try {
            $this->productGenerateData->execute();
            $output->writeln(sprintf('Generate product successfully!'));
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
        }
    }
}
