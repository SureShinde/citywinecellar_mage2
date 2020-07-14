<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Model\Order\Plugin;

use Amasty\OrderStatus\Model\ResourceModel\Status\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Status
{
    protected $scopeConfig;

    protected $amastyStatusCollectionFactory;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->amastyStatusCollectionFactory = $collectionFactory;
    }

    public function afterLoad($subject, $result)
    {
        if (!$result->getLabel()) {
            $code = $subject->getStatus();
            $statusesCollection = $this->amastyStatusCollectionFactory->create();

            if ($statusesCollection->getSize() > 0) {
                $hideState = $this->scopeConfig->getValue('amostatus/general/hide_state');
                $statusLabel = '';

                foreach ($statusesCollection->getStates() as $state) {
                    foreach ($statusesCollection as $status) {
                        if ($status->getData('is_active') && !$status->getData('is_system')) {
                            // checking if we should apply status to the current state
                            $parentStates = array();

                            if ($status->getParentState()) {
                                $parentStates = explode(',', $status->getParentState());
                            }

                            if (!$parentStates || in_array($state['value'], $parentStates)) {
                                $elementName = $state['value'] . '_' . $status->getAlias();

                                if ($code == $elementName) {
                                    $statusLabel = ($hideState ? '' : $state['label'] . ': ') . __($status->getStatus());

                                    break(2);
                                }
                            }
                        }
                    }
                }

                $status->setLabel($statusLabel);
                $status->setStoreLabel($statusLabel);
                $result = $status;
            }
        }

        return $result;
    }
}
