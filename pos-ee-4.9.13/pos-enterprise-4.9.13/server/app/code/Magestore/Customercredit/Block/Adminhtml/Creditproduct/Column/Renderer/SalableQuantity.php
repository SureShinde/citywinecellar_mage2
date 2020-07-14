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
 * Class SalableQuantity
 * @package Magestore\Customercredit\Block\Adminhtml\Creditproduct\Column\Renderer
 */
class SalableQuantity extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
            /** @var \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku $getSalableQuantityDataBySku */
            $getSalableQuantityDataBySku = $this->objectManager->get('Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku');
            $salableQuanties = $getSalableQuantityDataBySku->execute($row['sku']);
            $result = '<div class="data-grid-cell-content"><ul style="list-style-type: none; margin: 0; padding: 0">';
            foreach ($salableQuanties as $salableQuanty) {
                if (!$salableQuanty['manage_stock']) {
                    $result .= '<span >' . __("No manage stock") . '</span>';
                } else {
                    $result .= '<li><strong>' . $salableQuanty['stock_name'] . '</strong> : ';
                    $result .= '<span>' . $salableQuanty['qty'] . '</span></li>';
                }
            }
            $result .= '</ul></div>';
            return $result;
        }
        return "";
    }
}
