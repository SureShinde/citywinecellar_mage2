<?php
/**
 * Magestore.
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
 * @package     Magestore_Storepickup
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storepickup\Model\Store\Option;

/**
 * Class SourceCode
 * @package Magestore\Storepickup\Model\Store\Option
 */
class SourceCode implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\Storepickup\Helper\Data
     */
    protected $storePickupHelper;

    /**
     * SourceCode constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Storepickup\Helper\Data $storePickupHelper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Storepickup\Helper\Data $storePickupHelper
    )
    {
        $this->objectManager = $objectManager;
        $this->storePickupHelper = $storePickupHelper;
    }

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        $result = [];
        if ($this->storePickupHelper->isMSISourceEnable()) {
            /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
            $sourceRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
            $sources = $sourceRepository->getList()->getItems();
            foreach ($sources as $source) {
                $result[] = ['value' => $source->getSourceCode(), 'label' => $source->getName()];
                $options[] = $source->getName();
            }
        }
        return $result;
    }
}
