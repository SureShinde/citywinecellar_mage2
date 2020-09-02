<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Supplier;

use \Magento\Framework\App\ObjectManager;
use Magestore\DropshipSuccess\Api\Data\SupplierPricelistUploadInterface;

/**
 * Sales order history block
 */
class Pricelist extends AbstractSupplier
{
    /**
     * @var string
     */
    protected $_template = 'supplier/pricelist.phtml';

    protected $pricelist;

    /**
     * @return bool|\Magestore\DropshipSuccess\Model\ResourceModel\Supplier\PricelistUpload\Collection
     */
    public function getPricelist()
    {
        if (!($supplierId = $this->supplierSession->getSupplierId())) {
            return false;
        }
        if (!$this->pricelist) {
            /** @var \Magestore\DropshipSuccess\Model\ResourceModel\Supplier\PricelistUpload pricelist */
            $this->pricelist = $this->pricelistUploadService->getPricelistUploadBySupplierId($supplierId);
        }
        return $this->pricelist;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getPricelist()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'supplier.pricelist.pager'
            )->setCollection(
                $this->getPricelist()
            );
            $this->setChild('pager', $pager);
            $this->getPricelist()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get csv sample link
     *
     * @return mixed
     */
    public function getCsvSampleLink() {
        $url = $this->getUrl('dropship/supplier/downloadsample');
        return $url;
    }

    /**
     * Get upload link
     *
     * @return mixed
     */
    public function getUploadLink()
    {
        return $this->getUrl('dropship/supplier/uploadPricelist');
    }

    /**
     * @param SupplierPricelistUploadInterface $pricelistUpload
     * @return string
     */
    public function getDownloadLink(SupplierPricelistUploadInterface $pricelistUpload)
    {
        return $this->pricelistUploadService->getPricelistUploadLink($pricelistUpload);
    }

    /**
     * Get download link
     *
     * @return mixed
     */
    public function getDownloadUrl($supplierId, $fileName, $fileUpload)
    {
        return $this->getUrl('dropship/supplier/downloadPriceList', ['supplier_id' => $supplierId,
            'filename' => $fileName, 'file_upload' => $fileUpload]);
    }
}
