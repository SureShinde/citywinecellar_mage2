<?php
namespace Magestore\PosSampleData\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 * Ensures default authorization mode is set if upgrading from earlier versions
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magestore\PosSampleData\Helper\BackOrderTestCaseData
     */
    protected $backOrderTestCaseData;
    /**
     * @var \Magestore\PosSampleData\Helper\SampleSource
     */
    protected $sampleSource;

    /**
     * UpgradeData constructor.
     * @param \Magestore\PosSampleData\Helper\BackOrderTestCaseData $backOrderTestCaseData
     * @param \Magestore\PosSampleData\Helper\SampleSource $sampleSource
     */
    public function __construct(
        \Magestore\PosSampleData\Helper\BackOrderTestCaseData $backOrderTestCaseData,
        \Magestore\PosSampleData\Helper\SampleSource $sampleSource
    )
    {
        $this->backOrderTestCaseData = $backOrderTestCaseData;
        $this->sampleSource = $sampleSource;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        // Used update query because all scopes needed to have this value updated and this is a fast, simple approach
        if (version_compare($context->getVersion(), '0.0.0.2', '<')) {
            $this->backOrderTestCaseData->execute();
        }
        // create new source
        if (version_compare($context->getVersion(), '0.0.0.3', '<')) {
            $this->sampleSource->execute();
        }
    }
}
