<?php
namespace Magestore\PosSampleData\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magestore\PosSampleData\Model\FixtureGenerator\Customer\GenerateDataSetForStability as CustomerGenerateDataSet;
use Magento\Catalog\Model\Product;

/**
 * Class UpgradeData
 *
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
     * @var CustomerGenerateDataSet
     */
    protected $customerGenerateDataSet;
    /**
     * @var \Magento\Eav\Setup\EavSetup
     */
    protected $eavSetup;
    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $entityType;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * UpgradeData constructor.
     *
     * @param \Magestore\PosSampleData\Helper\BackOrderTestCaseData $backOrderTestCaseData
     * @param \Magestore\PosSampleData\Helper\SampleSource $sampleSource
     * @param CustomerGenerateDataSet $customerGenerateDataSet
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @param \Magento\Eav\Model\Entity\Type $entityType
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     */
    public function __construct(
        \Magestore\PosSampleData\Helper\BackOrderTestCaseData $backOrderTestCaseData,
        \Magestore\PosSampleData\Helper\SampleSource $sampleSource,
        CustomerGenerateDataSet $customerGenerateDataSet,
        \Magento\Eav\Setup\EavSetup $eavSetup,
        \Magento\Eav\Model\Entity\Type $entityType,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory
    ) {
        $this->backOrderTestCaseData = $backOrderTestCaseData;
        $this->sampleSource = $sampleSource;
        $this->customerGenerateDataSet = $customerGenerateDataSet;
        $this->eavSetup = $eavSetup;
        $this->entityType = $entityType;
        $this->productFactory = $productFactory;
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * Upgrade process
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
        // Create product configurable attribute
        if (version_compare($context->getVersion(), '0.0.0.5', '<')) {
            $data = [
                'group' => 'General',
                'attribute_set_id' => $this->productFactory->create()->getDefaultAttributeSetId(),
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Pos Configurable Attribute',
                'input' => 'select',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => false,
                'sort_order' => 101,
                'option' => [
                    'values' => [
                        'One',
                        'Two'
                    ]
                ]
            ];

            $this->eavSetup->removeAttribute(
                $this->entityType->loadByCode(Product::ENTITY)->getData('entity_type_id'),
                'pos_configurable'
            );
            $this->eavSetup->addAttribute(
                Product::ENTITY,
                'pos_configurable',
                $data
            );
        }
        // update cart price rules
        if (version_compare($context->getVersion(), '0.0.0.6', '<')) {
            /** @var \Magento\SalesRule\Model\Rule $rule */
            $rule = $this->ruleFactory->create();
            $rule->load(4);
            if ($rule->getId()) {
                $rule->setUsesPerCustomer(0)->save();
            }
        }
    }
}
