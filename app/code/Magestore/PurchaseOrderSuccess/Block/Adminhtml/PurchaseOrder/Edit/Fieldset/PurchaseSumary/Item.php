<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary;

use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;

/**
 * Class Item
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Fieldset\Grid
 */
class Item extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepositoryInterface;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_products';

    /**
     * @var array
     */
    protected $editFields = ['cost', 'tax', 'discount', 'qty_orderred'];

    /**
     * @var string
     */
    protected $originCostField = 'original_cost';

    /**
     * @var array
     */
    protected $selectedId;

    /**
     * @var array
     */
    protected $selectedProductData = [];

    protected $purchaseOrder;

    /**
     * Item constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\CollectionFactory $collectionFactory
     * @param \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepositoryInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\CollectionFactory $collectionFactory,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepositoryInterface,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->collectionFactory = $collectionFactory;
        $this->purchaseOrderRepositoryInterface = $purchaseOrderRepositoryInterface;
        $this->jsonEncoder = $jsonEncoder;
        $this->backendHelper = $backendHelper;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId("purchaseorder_list_item");
        $this->setDefaultSort("purchase_order_item_id");
        $this->setUseAjax(true);
    }

    /**
     * Set hidden input field name for selected products
     * 
     * @param $name
     */
    public function setHiddenInputField($name){
        $this->hiddenInputField = $name;
    }

    /**
     * get hidden input field name for selected products
     *
     * @return string
     */
    public function getHiddenInputField(){
        return $this->hiddenInputField;
    }

    /**
     * get origin cost for selected products
     *
     * @return string
     */
    public function getOriginCostField(){
        return $this->originCostField;
    }

    /**
     * Prepare collection for grid purchase order item
     * 
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->getDataColllection();
        $collection = $this->modifyCollection($collection);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Get Collection for grid purchase order item
     *
     * @return Collection
     */
    public function getDataColllection(){
        $purchaseOrderId = $this->getRequest()->getParam('id', null);
        $collection = $this->collectionFactory->create();
        $collection->setPurchaseOrderToFilter($purchaseOrderId);
        return $collection;
    }

    /**
     * Function to modify collection
     * 
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function modifyCollection(\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection){
        $selectedProduct = $this->getSelectedProducts();
        $data = $this->backendHelper->prepareFilterString($this->getRequest()->getParam('filters'));
        if(array_key_exists('in_purchase_order', $data)){
            $condition = 'nin';
            if($data['in_purchase_order'] == '1')
                $condition = 'in';
            $collection->addFieldToFilter('product_id', [$condition => $selectedProduct]);
        }
        $collection->getSelect()
            ->order(new \Zend_Db_Expr('FIELD(product_id, "' . implode('","', $selectedProduct) . '") DESC'));
        return $collection;
    }

    /**
     * Prepare columns for grid purchase order item
     * 
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {       
        $this->addColumn(
            "in_purchase_order",
            [
                "type" => "checkbox",
                "name" => "in_purchase_order",
                "values" => $this->getSelectedProducts(),
                "index" => "product_id",
                "filter" => false,
                "use_index" => "product_id",
                "header_css_class" => "col-select col-massaction",
                "column_css_class" => "col-select col-massaction"
            ]
        );
        $this->addColumn("product_sku",
            [
                "header" => __("SKU"),
                "index" => "product_sku",
                "sortable" => true,
            ]
        );
        $this->addColumn("product_name",
            [
                "header" => __("Product Name"),
                "index" => "product_name",
                "sortable" => true,
            ]
        );
        $this->addColumn("product_supplier_sku",
            [
                "header" => __("Supplier SKU"),
                "index" => "product_supplier_sku",
                "sortable" => true,
            ]
        );
        $purchaseOrder = $this->getCurrentPurchaseOrder();
        $this->addColumn("original_cost",
            [
                "header" => __("Current Cost") . '('.$purchaseOrder->getCurrencyCode().')',
                "index" => "original_cost",
                'type' => 'number',
                "sortable" => true,
                'rate' => '1'
            ]
        );
        $this->addColumn("cost",
            [
                "header" => __("Purchase Cost") . ' ('.$purchaseOrder->getCurrencyCode().')',
                'renderer' => 'Magestore\PurchaseOrderSuccess\Block\Adminhtml\Grid\Column\Renderer\Text',
                "index" => "cost",
                'type' => 'number',
                "editable" => true,
                "sortable" => true
            ]
        );
        $this->addColumn("tax",
            [
                "header" => __("Tax (%)"),
                'renderer' => 'Magestore\PurchaseOrderSuccess\Block\Adminhtml\Grid\Column\Renderer\Text',
                "index" => "tax",
                'type' => 'number',
                "editable" => true,
                "sortable" => true
            ]
        );
        $this->addColumn("discount",
            [
                "header" => __("Discount (%)"),
                'renderer' => 'Magestore\PurchaseOrderSuccess\Block\Adminhtml\Grid\Column\Renderer\Text',
                "index" => "discount",
                'type' => 'number',
                "editable" => true,
                "sortable" => true
            ]
        );
        $this->addColumn("qty_orderred",
            [
                "header" => __("Qty Orderring"),
                'renderer' => 'Magestore\PurchaseOrderSuccess\Block\Adminhtml\Grid\Column\Renderer\Text',
                "index" => "qty_orderred",
                'type' => 'number',
                "editable" => true,
                "sortable" => true
            ]
        );
        $this->addColumn("delete",
            [
                "header" => __("Action"),
                'renderer' => 'Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary\Item\Delete',
                'filters' => false,
                'sortable' => false,
            ]
        );
        $this->modifyColumns();
        $this->_eventManager->dispatch('prepare_purchase_order_grid_item', ['object' => $this]);
        return parent::_prepareColumns();
    }

    /**
     * function to add, remove or modify product grid columns
     *
     * @return $this
     */
    public function modifyColumns(){
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl("*/purchaseOrder_product/grid", ["_current" => true]);
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/purchaseOrder_product/update', ["_current" => true]);
    }
    
    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/purchaseOrder_product/delete');
    }

    /**
     * @return string
     */

    public function getUpdateCostUrl()
    {
        return $this->getUrl('*/purchaseOrder_product/updatecost');
    }

    public function getReloadTotalUrl()
    {
        return $this->getUrl('*/purchaseOrder_product/reloadTotal', ["_current" => true]);
    }

    /**
     * @return array
     */
    public function getSelectedProducts()
    {
        if(empty($this->selectedId)){
            $collection = $this->getDataColllection()
                ->addFieldToFilter(PurchaseOrderItemInterface::QTY_ORDERRED, ['lteq' => 0]);
            /** @var PurchaseOrderItemInterface $item */
            foreach ($collection as $item) {
                $this->selectedProductData[$item->getProductId()] = [
                    PurchaseOrderItemInterface::COST => $item->getCost(),
                    PurchaseOrderItemInterface::COST . '_old' => $item->getCost(),
                    PurchaseOrderItemInterface::TAX => $item->getTax(),
                    PurchaseOrderItemInterface::TAX . '_old' => $item->getTax(),
                    PurchaseOrderItemInterface::DISCOUNT => $item->getDiscount(),
                    PurchaseOrderItemInterface::DISCOUNT . '_old' => $item->getDiscount(),
                    PurchaseOrderItemInterface::QTY_ORDERRED => $item->getQtyOrderred(),
                    PurchaseOrderItemInterface::QTY_ORDERRED . '_old' => $item->getQtyOrderred(),
                    PurchaseOrderItemInterface::ORIGINAL_COST => $item->getOriginalCost(),
                ];
            }
            $this->selectedId = array_keys($this->selectedProductData);
        }
        return $this->selectedId;
    }

    /**
     * @return string
     */
    public function getSelectedProductData()
    {
        return $this->jsonEncoder->encode($this->selectedProductData);
    }

    /**
     * @return array
     */
    public function getEditFields(){
        return json_encode($this->editFields);
    }

    /**
     * @return string
     */
    public function getPriceListJson()
    {
        if (!$this->_scopeConfig->getValue('suppliersuccess/pricelist/enable')) {
            $priceList = [];
            return $this->jsonEncoder->encode($priceList);
        }
        $puchaseOrder = $this->getCurrentPurchaseOrder();
        $time = $puchaseOrder->getPurchasedAt();
        $supplierId = $puchaseOrder->getSupplierId();
        /** @var \Magestore\SupplierSuccess\Service\Supplier\PricingListService $supplierService */
        $supplierService =  \Magento\Framework\App\ObjectManager::getInstance()->create(
            'Magestore\SupplierSuccess\Service\Supplier\PricingListService'
        );
        $priceList = $supplierService->getProductCost(null, $supplierId, $time);
        $priceListJson = [];
        foreach ($priceList as $price){
            $price['cost'] = $this->priceCurrency->convert($price['cost'] * $puchaseOrder->getCurrencyRate());
            $priceListJson[] = $price;
        }
        return $this->jsonEncoder->encode($priceListJson);
    }

    public function getCurrentPurchaseOrder(){
        if(!$this->purchaseOrder){
            $purchaseOrderId = $this->getRequest()->getParam('id');
            $this->purchaseOrder = $this->purchaseOrderRepositoryInterface->get($purchaseOrderId);
        }
        return $this->purchaseOrder;
    }
}