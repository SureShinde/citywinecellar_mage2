<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Model\Order\Plugin;

use Amasty\OrderStatus\Model\ResourceModel\Status\CollectionFactory;

//phpcs:ignoreFile
class Config extends \Magento\Sales\Model\Order\Config
{
    protected $_objectManager;

    protected $scopeConfig;

    protected $amastyOrderStatusCollection;

    protected $amastyOrderStatus;

    public function __construct(
        \Magento\Sales\Model\Order\StatusFactory $orderStatusFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $orderStatusCollectionFactory,
        \Magento\Framework\App\State $state,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        CollectionFactory $amastyOrderStatusCollection
    ) {
        $this->_objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
        $this->amastyOrderStatusCollection = $amastyOrderStatusCollection;

        parent::__construct($orderStatusFactory, $orderStatusCollectionFactory, $state);
    }

    public function aroundGetStateStatuses($subject, $proceed, $stateToGetFor, $addLabels = true)
    {
        $statusCollection = $this->amastyOrderStatusCollection->create();
        $statuses = $proceed($stateToGetFor);

        if ($statusCollection->getSize() > 0) {
            $hideState = $this->scopeConfig->getValue('amostatus/general/hide_state');

            if (!is_array($stateToGetFor)) {
                $stateToGetFor = [$stateToGetFor];
            }

            foreach ($stateToGetFor as $getFor) {
                foreach ($statusCollection->getStates() as $state) {
                    if ($getFor == $state['value']) {
                        foreach ($statusCollection as $status) {
                            if ($status->getData('is_active') && !$status->getData('is_system')) {
                                // checking if we should apply status to the current state
                                $parentStates = [];

                                if ($status->getParentState()) {
                                    $parentStates = explode(',', $status->getParentState());
                                }

                                if (!$parentStates || in_array($state['value'], $parentStates)) {
                                    $elementName = $state['value'] . '_' . $status->getAlias();

                                    if ($addLabels) {
                                        $statuses[$elementName] = ($hideState ? '' : $state['label'] . ': ') . __($status->getStatus());
                                    } else {
                                        $statuses[] = $elementName;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $statuses;
    }

    public function aroundGetStatusLabel($subject, $proceed, $code)
    {
        $statusLabel = $proceed($code);

        if (empty($statusLabel) || (is_object($statusLabel) && !$statusLabel->getText())) {
            $statusCollection = $this->amastyOrderStatusCollection->create();

            if ($statusCollection->getSize() > 0) {
                $hideState = $this->scopeConfig->getValue('amostatus/general/hide_state');

                foreach ($statusCollection->getStates() as $state) {
                    foreach ($statusCollection as $status) {
                        if ($status->getData('is_active') && !$status->getData('is_system')) {
                            // checking if we should apply status to the current state
                            $parentStates = [];

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
            }
        }

        return $statusLabel;
    }

    public function afterGetVisibleOnFrontStatuses($subject, $result)
    {
        $statusCollection = $this->amastyOrderStatusCollection->create();
        $statuses = $statusCollection->getAllStateStatuses();
        $result = array_merge($result, $statuses);

        return $result;
    }

    public function afterGetStatuses($subject, $result)
    {
        $statuses = [];
        $statusCollection = $this->amastyOrderStatusCollection->create();

        if ($statusCollection->getSize()) {
            $hideState = $this->scopeConfig->getValue('amostatus/general/hide_state');

            foreach ($statusCollection->getStates() as $state) {
                foreach ($statusCollection as $status) {
                    if ($status->getIsActive() && !$status->getIsSystem()) {
                        $parentStates = [];

                        if ($status->getParentState()) {
                            $parentStates = explode(',', $status->getParentState());
                        }

                        if (!$parentStates || in_array($state['value'], $parentStates)) {
                            $key = $state['value'] . '_' . $status->getAlias();
                            $statuses[$key] = ($hideState ? '' : $state['label'] . ': ') . __($status->getStatus());
                        }
                    }
                }
            }
        }

        return array_merge($result, $statuses);
    }
}
