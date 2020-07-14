<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\AdjustStock\Ui\DataProvider\AdjustStock\Form\Modifier;

use Magento\Framework\UrlInterface;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Form;
use Magestore\AdjustStock\Api\Data\AdjustStock\AdjustStockInterface;

/**
 * Class Related
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AdjustStock extends \Magestore\AdjustStock\Ui\DataProvider\Form\Modifier\Dynamic
{
    /**
     * @var \Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product\CollectionFactory
     */
    protected $collection;

    /**
     * @var \Magestore\AdjustStock\Model\AdjustStockFactory
     */
    protected $adjustStockFactory;

    /**
     * @var \Magestore\AdjustStock\Model\ResourceModel\AdjustStock
     */
    protected $adjustStockResource;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magestore\AdjustStock\Model\Source\Adminhtml\Source
     */
    protected $sourceSelect;

    /**
     * @var \Magestore\AdjustStock\Model\AdjustStock\Options\Status
     */
    protected $adjustStockStatus;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Generate constructor.
     * @param \Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product\CollectionFactory $collectionFactory
     * @param \Magestore\AdjustStock\Model\AdjustStockFactory $adjustStockFactory
     * @param \Magestore\AdjustStock\Api\AdjustStock\AdjustStockManagementInterface $adjustStockManagement
     * @param \Magestore\AdjustStock\Model\ResourceModel\AdjustStock $adjustStockResource
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\AdjustStock\Model\Source\Adminhtml\Source $sourceSelect
     * @param \Magestore\AdjustStock\Model\AdjustStock\Options\Status $adjustStockStatus
     * @param array $_modifierConfig
     */
    public function __construct(
        \Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product\CollectionFactory $collectionFactory,
        \Magestore\AdjustStock\Model\AdjustStockFactory $adjustStockFactory,
        \Magestore\AdjustStock\Model\ResourceModel\AdjustStock $adjustStockResource,
        UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\AdjustStock\Api\AdjustStock\AdjustStockManagementInterface $adjustStockManagement,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\AdjustStock\Model\Source\Adminhtml\Source $sourceSelect,
        \Magestore\AdjustStock\Model\AdjustStock\Options\Status $adjustStockStatus,
        array $_modifierConfig = []
    ) {
        parent::__construct($urlBuilder, $request, $adjustStockManagement, $_modifierConfig);
        $this->collection = $collectionFactory->create();
        $this->adjustStockFactory = $adjustStockFactory;
        $this->adjustStockResource = $adjustStockResource;
        $this->adjustStockStatus = $adjustStockStatus;
        $this->sourceSelect = $sourceSelect;
        $this->urlBuilder = $urlBuilder;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Get current Adjustment
     *
     * @return Adjustment
     * @throws NoSuchEntityException
     */
    public function getCurrentAdjustment()
    {
        $adjustStock = $this->coreRegistry->registry('current_adjuststock');
        return $adjustStock;
    }

    /**
     * Get adjust stock status
     *
     * @return string
     */
    public function getAdjustStockStatus()
    {
        $adjustStock = $this->getCurrentAdjustment();
        if($adjustStock->getId()){
            return $adjustStock->getData('status');
        }
        return null;
    }

    /**
     * is disabled element
     *
     * @param
     * @return
     */
    public function isDisabledElement()
    {
        if ($this->request->getParam('id'))
            return 'disabled';
        return false;
    }

    /**
     * get collapsible
     *
     * @param
     * @return boolean
     */
    public function getCollapsible(){
        if ($this->getAdjustStockStatus() != '1')
            return $this->_collapsible;
        return false;
    }

    /**
     * get group label
     *
     * @param
     * @return boolean
     */
    public function getGroupLabel(){
        if ($this->getAdjustStockStatus() != '1')
            return $this->_groupLabel;
        return '';
    }

    /**
     * get modify tmpl
     *
     * @param
     * @return
     */
    public function getModifyTmpl($type)
    {
        if ($this->getAdjustStockStatus() == AdjustStockInterface::STATUS_COMPLETED) {
            switch ($type){
                case 'input':
                    return static::TMPL_TEXT_LABEL;
                    break;
                case 'textarea':
                    return static::TMPL_TEXTAREA_LABEL;
                    break;
                case 'select':
                    return static::TMPL_SELECT_LABEL;
                    break;
                default:
                    return static::TMPL_TEXT_LABEL;
            }
        }

        if ($this->getAdjustStockStatus() != null) {
            if ($type == 'select'){
                return static::TMPL_SELECT_LABEL;
            }
        }
        return parent::getModifyTmpl($type);
    }
}
