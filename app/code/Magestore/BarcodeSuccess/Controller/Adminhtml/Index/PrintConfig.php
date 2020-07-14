<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Index;

use Magento\Ui\Component\MassAction\Filter;

/**
 * Class PrintConfig
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\Index
 */
class PrintConfig extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractIndex
{
    protected $likeCondition = ['barcode', 'product_sku', 'supplier_code'];
    protected $dateCondition = ['purchased_time'];
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = parent::execute();
        $resultPage->getConfig()->getTitle()->prepend(__('Barcode Printing Configuration'));
        $source = $this->getRequest()->getParam('source');
        $selected = $this->getRequest()->getParam(Filter::SELECTED_PARAM);
        $excluded = $this->getRequest()->getParam(Filter::EXCLUDED_PARAM);
        $collection = $this->helper->getModel('Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\Collection');
        if ($this->getRequest()->getParam('filters')) {
            $filters = $this->getRequest()->getParam('filters');
            if(isset($filters['placeholder'])){
                unset($filters['placeholder']);
            }
            if(isset($source) && $source == 'product_listing' && count($filters) > 0){
                $productCollectionFactory = $this->_objectManager
                    ->create(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
                $filterMassAction = $this->_objectManager
                    ->get( \Magento\Ui\Component\MassAction\Filter::class);
                $productCollection = $filterMassAction->getCollection($productCollectionFactory);
                $selected = $productCollection->getAllIds();
            }else{
                foreach ($filters as $key => $filter){
                    if (in_array($key, $this->likeCondition)) {
                        $collection->addFieldToFilter($key, ['like' => '%' . $filter . '%']);
                    } elseif (in_array($key, $this->dateCondition)) {
                        if(isset($filter['from'])) {
                            $collection->addFieldToFilter($key, ['gteq' => (new \Datetime($filter['from']))->format('Y-m-d H:i:s')]);
                        }
                        if(isset($filter['to'])) {
                            $collection->addFieldToFilter($key, ['lteq' => (new \Datetime($filter['to']))->format('Y-m-d H:i:s')]);
                        }
                    } else {
                        $collection->addFieldToFilter($key, ['eq' => $filter]);
                    }
                }
            }
        }

        if(isset($source) && $source == 'product_listing'){
            if(is_array($excluded)){
                $collection->addFieldToFilter('product_id', ['nin' => $excluded]);
            }
            if(is_array($selected)){
                $collection->addFieldToFilter('product_id',["in" => $selected]);
            }
        }
        if(isset($source) && $source == 'product_detail'){
            $productId = $this->locator->get('current_product_id_to_print');
            $collection->addFieldToFilter('product_id',$productId);
            if(is_array($excluded)){
                $collection->addFieldToFilter('id', ['nin' => $excluded]);
            }
            if(is_array($selected)){
                $collection->addFieldToFilter('id',["in" => $selected]);
            }
        }
        if(isset($source) && $source == 'created_history_detail'){
            $historyId = $this->locator->getCurrentBarcodeHistory();
            $collection->addFieldToFilter('history_id',$historyId);
            if(is_array($excluded)){
                $collection->addFieldToFilter('id', ['nin' => $excluded]);
            }
            if(is_array($selected)){
                $collection->addFieldToFilter('id',["in" => $selected]);
            }
        }
        if(!isset($source)){
            if(is_array($excluded)){
                $collection->addFieldToFilter('id', ['nin' => $excluded]);
            }
            if(is_array($selected)){
                $collection->addFieldToFilter('id',["in" => $selected]);
            }
        }
        $ids = $collection->getAllIds();
        $this->locator->add('current_barcode_ids_to_print', $ids);

        $this->locator->remove('print_inline_edit_qty');
        return $resultPage;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_BarcodeSuccess::print_barcode');
    }
}
