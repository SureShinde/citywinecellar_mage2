<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */
namespace Magestore\Customercredit\Block\Adminhtml\Creditproduct\Column\Renderer;

use Magento\Framework\Locale\Bundle\DataBundle;

/**
 * Class QtyPerSource
 * @package Magestore\Customercredit\Block\Adminhtml\Creditproduct\Column\Renderer
 */
class QtyPerSource extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * QtyPerSource constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->objectManager = $objectManager;
    }

    /**
     * Renders grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($sku = $row->getData('sku')) {
            /** @var \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItemsBySku */
            $getSourceItemsBySku = $this->objectManager->get('Magento\InventoryApi\Api\GetSourceItemsBySkuInterface');
            $sourceRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
            $sourceItems = $getSourceItemsBySku->execute($sku);
            $result = '<div class="data-grid-cell-content"><ul style="list-style-type: none; margin: 0; padding: 0">';
            foreach ($sourceItems as $sourceItem) {
                $source = $sourceRepository->get($sourceItem->getSourceCode());
                $qty = (float)$sourceItem->getQuantity();
                $result .= '<li><strong>' . $source->getName() . '</strong> : <span>' . $qty . '</span></li>';
            }
            $result .= '</ul></div>';
            return $result;
        }
        return "";
    }
}
