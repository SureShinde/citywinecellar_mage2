<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Model\HistoricalReport;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Notification\MessageInterface;
/**
 * Class ConvertToCsv
 * @package Magestore\ReportSuccess\Model\HistoricalReport
 */
class ConvertToCsv
{
    /**
     *
     */
    const PAGE_SIZE = 200;
    /**
     * @var \Magestore\ReportSuccess\Model\ResourceModel\Report\HistoricalStock\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $directory;
    /**
     * @var \Magento\Framework\Archive
     */
    protected $archive;
    /**
     * @var array
     */
    protected $columns = [
        'sku',  'qty_on_hand', 'available_qty', 'mac', 'stock_Value'
    ];
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var mixed
     */
    protected $isEnabledBarcode;
    /**
     * @var mixed
     */
    protected $barcode;
    /**
     * @var \Magestore\ReportSuccess\Model\Source\Adminhtml\Product\BarcodeAttribute
     */
    protected $barcodeAttribute;
    /**
     * @var \Magento\AdminNotification\Model\InboxFactory
     */
    protected $inboxFactory;
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $url;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magestore\ReportSuccess\Api\ReportManagementInterface
     */
    protected $reportManagement;

    /**
     * @var \Magestore\ReportSuccess\Model\ResourceModel\Report\StockDetails\Collection
     */
    protected $collectionDetail;

    /**
     * ConvertToCsv constructor.
     * @param Filesystem $filesystem
     * @param \Magestore\ReportSuccess\Model\ResourceModel\Report\HistoricalStock\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Archive $archive
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\ReportSuccess\Model\Source\Adminhtml\Product\BarcodeAttribute $barcodeAttribute
     * @param \Magento\AdminNotification\Model\InboxFactory $inboxFactory
     * @param \Magento\Backend\Model\UrlInterface $url
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement
     * @param \Magestore\ReportSuccess\Model\ResourceModel\Report\StockDetails\Collection $collectionDetail
     */
    public function __construct(
        Filesystem $filesystem,
        \Magestore\ReportSuccess\Model\ResourceModel\Report\HistoricalStock\CollectionFactory $collectionFactory,
        \Magento\Framework\Archive $archive,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\ReportSuccess\Model\Source\Adminhtml\Product\BarcodeAttribute $barcodeAttribute,
        \Magento\AdminNotification\Model\InboxFactory $inboxFactory,
        \Magento\Backend\Model\UrlInterface $url,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement,
        \Magestore\ReportSuccess\Model\ResourceModel\Report\StockDetails\Collection $collectionDetail
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->archive = $archive;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->barcodeAttribute = $barcodeAttribute;
        $this->inboxFactory = $inboxFactory;
        $this->url = $url;
        $this->dateTime = $dateTime;
        $this->isEnabledBarcode = $this->scopeConfig->getValue('reportsuccess/general/enable_barcode_in_report');
        $this->barcode = $this->scopeConfig->getValue('reportsuccess/general/barcode');
        $this->localeDate = $localeDate;
        $this->reportManagement = $reportManagement;
        $this->collectionDetail = $collectionDetail;
    }
    /**
     * @return array
     */
    public function getHeaders(){
        $header = [];
        $header['sku'] = 'SKU';
        if ($this->isEnabledBarcode && $this->barcode != 'sku'){
            $barcodeAttributeArray = $this->barcodeAttribute->toOptionArray();
            foreach ($barcodeAttributeArray as $barcode){
                if ($barcode['value'] == $this->barcode){
                    $header['barcode'] = $barcode['label'];
                }
            }
        }
        return array_merge($header, [
            'name' => __('Product name'),
            'qty_on_hand' => __('Qty on-hand'),
            'available_qty' => __('Available qty'),
            'mac' => __('Cost (Moving Average Cost)'),
            'stock_value' => __('Stock Value'),
        ]);
    }
    /**
     * @param null $warehouse
     * @param bool $notification
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCsvFile($warehouse = null, $notification = false)
    {
        $baseCurrencyCode = $this->storeManager->getStore()->getBaseCurrencyCode();
        $collection = $this->collectionFactory->create();

        if ($this->reportManagement->isMSIEnable()) {
            $qtyToShipSql = $this->collectionDetail->createQtyToShipTempTable();
            $collection->getSelect()->joinLeft(
                ['shipTable' => $qtyToShipSql],
                'warehouse_product.sku = shipTable.sku and warehouse_product.source_code = shipTable.source_code',
                '');
            $collection->getSelect()->group('warehouse_product.sku');
            $collection->getSelect()->columns([
                    'available_qty' => new \Zend_Db_Expr('SUM( IFNULL(warehouse_product.quantity,0) - IFNULL(shipTable.qty_to_ship,0))')]
            );
        }
        else{
            $collection->getSelect()->columns([
                'available_qty' => new \Zend_Db_Expr('IFNULL(IFNULL(SUM(warehouse_product.qty),0),0)')
            ]);
        }

        $timeCreatedAt = $this->localeDate->date();
        $timeByTimeZone = $timeCreatedAt->format('YmdHis');
        $timeByTimeZoneFormatCsv = $timeCreatedAt->format('m/d/y h:i:s A');
        if ($warehouse){
            if($this->reportManagement->isMSIEnable()){
                $collection->getSelect()->where('warehouse_product.source_code = ?',$warehouse->getSourceCode());
                $warehouseName = $warehouse->getName();
                $fileName = $warehouse->getSourceCode(). '_' .$timeByTimeZone;
                $compressName = $warehouse->getSourceCode(). '_' .$this->localeDate->convertConfigTimeToUtc($timeCreatedAt, 'YmdHis');
            }else{
                $collection->addWarehouseToFilter($warehouse->getWarehouseId());
                $warehouseName = $warehouse->getWarehouseName();
                $fileName = $warehouse->getWarehouseCode(). '_' .$timeByTimeZone;
                $compressName = $warehouse->getWarehouseCode(). '_' .$this->localeDate->convertConfigTimeToUtc($timeCreatedAt, 'YmdHis');
            }
            //Compress name in GMT timezone
        } else {
            $warehouseName = $this->reportManagement->isMSIEnable() ? __("All Source") : __('All Warehouses');
            $fileName = 'all'. '_' .$timeByTimeZone;
            $compressName = 'all'. '_' .$this->localeDate->convertConfigTimeToUtc($timeCreatedAt, 'YmdHis');
            //Compress name in GMT timezone
        }
        $collection->setOrder('sku', 'ASC');
        $this->directory->create('historical_stock');
        $file = 'historical_stock/'. $fileName . '.csv';
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $stream->writeCsv([__('HISTORICAL STOCK')]);
        $stream->writeCsv([$warehouseName. ' - ' . $timeByTimeZoneFormatCsv]);
        $stream->writeCsv(array_values($this->getHeaders()));
        $pageSize = self::PAGE_SIZE;
        $page = 1;
        $totalCount = (int) $collection->getSize();
        while ($totalCount > 0) {
            $collectionPageSize = clone $collection;
            $collectionPageSize->setPageSize($pageSize);
            $collectionPageSize->setCurPage($page);
            $collectionPageSize->load();
            $items = $collectionPageSize->toArray();
            foreach ($items as $item) {
                $itemData = [];
                foreach ($this->getHeaders() as $key => $value){
                    if (in_array($key, array('mac', 'stock_value'))){
                        if ($item[$key]) {
                            $item[$key] = $this->priceCurrency->format($item[$key], false, 2, null, $baseCurrencyCode);
                        } else {
                            $item[$key] = null;
                        }
                    }
                    $itemData[] = $item[$key];
                }
                $stream->writeCsv($itemData);
            }
            $page++;
            $totalCount = $totalCount - $pageSize;
        }
        $stream->unlock();
        $stream->close();
        $fileCompress = 'historical_stock/'. $compressName . '.tgz';
        try {
            $this->archive->pack($this->directory->getAbsolutePath($file), $this->directory->getAbsolutePath($fileCompress));
            $this->directory->delete($this->directory->getAbsolutePath($file));
        } catch (\Exception $e){
            return;
        }
        if ($notification){
            $this->inboxFactory->create()->add(MessageInterface::SEVERITY_NOTICE, __('Generated Historical Report Successfully.'),
                __('A historical stock report has already been generated. Go to Historical Stock Report page to download this report.'));
        }
        return;
    }
}