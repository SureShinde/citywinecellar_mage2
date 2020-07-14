<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Helper;
use Magento\Framework\App\ResourceConnection;
use Magento\InventoryReservationsApi\Model\ReservationInterface;
/**
 * Class PurchaseOrderData
 * @package Magestore\PosSampleData\Helper
 */
class BackOrderTestCaseData extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SUPPLER_SAMPLE_DATA = [
        'supplier_code' => 'SS_SAMPLE',
        'supplier_name' => 'Samsung_Sample',
        'contact_name' => 'Samsung_Sample',
        'status' => 1
    ];

    const PRODUCT_OF_SUPPLIER = [
        [
            'entity_id' => 17,
            'sku' => '24-UG04',
            'name' => 'Zing Jump Rope'
        ],
        [
            'entity_id' => 42,
            'sku' => '24-WG01',
            'name' => 'Bolo Sport Watch'
        ],
        [
            'entity_id' => 44,
            'sku' => '24-WG02',
            'name' => 'Didi Sport Watch'
        ]
    ];

    const PRODUCT_BACKORDER_TEST = [
        'entity_id' => 17,
        'sku' => '24-UG04',
        'name' => 'Zing Jump Rope'
    ];

    const RESERVATION_PRODUCT = [
        'stock_id' => 1,
        'sku' => '24-UG04',
        'quantity' => -1,
        'metadata' => '{"event_type":"shipment_created","object_type":"order","object_id":0}'
    ];
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magestore\SupplierSuccess\Model\SupplierFactory
     */
    protected $supplierFactory;
    /**
     * @var \Magestore\SupplierSuccess\Model\Supplier\ProductFactory
     */
    protected $supplierProductFactory;
    /**
     * @var \Magento\Inventory\Model\SourceItemFactory
     */
    protected $collectionFactory;
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\SupplierSuccess\Model\SupplierFactory $supplierFactory,
        \Magestore\SupplierSuccess\Model\Supplier\ProductFactory $supplierProductFactory,
        \Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory $collectionFactory,
        ResourceConnection $resourceConnection
    )
    {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->supplierFactory = $supplierFactory;
        $this->supplierProductFactory = $supplierProductFactory;
        $this->collectionFactory = $collectionFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     *
     */
    public function execute()
    {
        $this->createSupplierSampleData();
        $this->forceBackOrderProduct();
    }

    /**
     * @return mixed
     */
    public function createSupplierSampleData()
    {
        $supplierModel = $this->supplierFactory->create()->setData(self::SUPPLER_SAMPLE_DATA);
        $supplierModel->save();
        foreach (self::PRODUCT_OF_SUPPLIER as $dataSupplier) {
            $supplierProductModel = $this->supplierProductFactory->create()->setData([
                'supplier_id' => $supplierModel->getId(),
                'product_id' => $dataSupplier['entity_id'],
                'product_sku' => $dataSupplier['sku'],
                'product_name' => $dataSupplier['name'],
                'product_supplier_sku' => null,
                'cost' => 100,
                'tax' => 0,
            ]);
            $supplierProductModel->save();
        }
        return $supplierModel;
    }

    /**
     *
     */
    public function forceBackOrderProduct()
    {
        $sourceItemModel = $this->collectionFactory->create()->addFieldToFilter('source_code', 'default')
            ->addFieldToFilter('sku', self::PRODUCT_BACKORDER_TEST['sku'])->getFirstItem();
        if ($sourceItemModel->getId()){
            $sourceItemModel->setData('quantity', 0);
            $sourceItemModel->save();
        }
        $this->addReservation();
    }

    /**
     *
     */
    public function addReservation()
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('inventory_reservation');

        $columns = [
            ReservationInterface::STOCK_ID,
            ReservationInterface::SKU,
            ReservationInterface::QUANTITY,
            ReservationInterface::METADATA,
        ];
        $data = [];
        $data[] = [
            'stock_id' => self::RESERVATION_PRODUCT['stock_id'],
            'sku' => self::RESERVATION_PRODUCT['sku'],
            'quantity' =>self::RESERVATION_PRODUCT['quantity'],
            'metadata' => self::RESERVATION_PRODUCT['metadata'],
        ];

        $connection->insertArray($tableName, $columns, $data);
    }
}