<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\ResourceModel;

use Amasty\Xlanding\Api\Data\PageInterface;
use Amasty\Xlanding\Model\Page as PageModel;
use Magento\Store\Model\Store;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\DB\Select;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Model\AbstractModel;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Amasty\Xlanding\Model\Page\Product\AdminhtmlDataProvider;

class Page extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const ENTITY_TYPE = 'amasty-xlanding-page';

    protected $urlRewriteFactory;
    protected $urlPersist;
    protected $_storeManager;

    /** @var  Manager */
    private $moduleManager;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var AdminhtmlDataProvider
     */
    private $productPositionDataProvider;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        StoreManagerInterface $storeManager,
        UrlRewriteFactory $urlRewriteFactory,
        UrlPersistInterface $urlPersist,
        Manager $moduleManager,
        ProductCollectionFactory $productCollectionFactory,
        AdminhtmlDataProvider $productPositionDataProvider,
        $connectionName = null
    ) {
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlPersist = $urlPersist;
        $this->_storeManager = $storeManager;
        $this->moduleManager = $moduleManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productPositionDataProvider = $productPositionDataProvider;
        return parent::__construct(
            $context,
            $connectionName
        );
    }

    protected function _construct()
    {
        $this->_init('amasty_xlanding_page', 'page_id');
    }

    protected function generateForAllStores(\Magento\Framework\Model\AbstractModel $object)
    {
        $urls = [];
        foreach ($this->_storeManager->getStores() as $store) {
            $urls[] = $this->createUrlRewrite($object, $store->getStoreId());
        }
        return $urls;
    }

    /**
     * Generate list of urls per store
     *
     * @param int[] $storeIds
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    protected function generateForSpecificStores(\Magento\Framework\Model\AbstractModel $object, $storeIds)
    {
        $urls = [];
        $existingStores = $this->_storeManager->getStores();
        foreach ($storeIds as $storeId) {
            if (!isset($existingStores[$storeId])) {
                continue;
            }
            $urls[] = $this->createUrlRewrite($object, $storeId);
        }
        return $urls;
    }

    public function load(AbstractModel $object, $value, $field = null)
    {
        //check if page is active
        if ($this->getPageId($object, $value, $field)) {
            parent::load($object, $value, $field);
        }
        return $this;
    }

    private function getPageId(AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        } elseif (!$field) {
            $field = $this->getIdFieldName();
        }

        $pageId = $value;
        if ($field != $this->getIdFieldName() || $object->getStoreId()) {
            //if loaded from frontend
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->where('is_active != ?', PageModel::STATUS_DISABLED);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $this->getIdFieldName())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $pageId = count($result) ? $result[0] : false;
        }

        return $pageId;
    }

    /**
     * @param AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());

            $object->setData('store_id', $stores);
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel $object
     * @param int $storeId
     * @param int $redirectType
     * @return UrlRewrite
     */
    protected function createUrlRewrite(\Magento\Framework\Model\AbstractModel $object, $storeId, $redirectType = 0)
    {
        return $this->urlRewriteFactory->create()
            ->setStoreId($storeId)
            ->setEntityType(self::ENTITY_TYPE)
            ->setEntityId($object->getId())
            ->setRequestPath($object->getIdentifier())
            ->setTargetPath('amasty_xlanding/page/view/page_id/' . $object->getId())
            ->setIsAutogenerated(1)
            ->setRedirectType($redirectType);
    }

    /**
     * @param int $pageId
     * @return array
     */
    public function lookupStoreIds($pageId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('amasty_xlanding_page_store'),
            'store_id'
        )->where(
            'page_id = ?',
            (int)$pageId
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param $identifier
     * @param $storeId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect(
            $identifier,
            $stores,
            [PageModel::STATUS_DYNAMIC, PageModel::STATUS_ACTIVE]
        );
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns('cp.page_id')
            ->order('cps.store_id DESC')
            ->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @param $identifier
     * @param $store
     * @param null $isActive
     * @return Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['cp' => $this->getMainTable()]
        )->join(
            ['cps' => $this->getTable('amasty_xlanding_page_store')],
            'cp.page_id = cps.page_id',
            []
        )->where('cp.identifier = ?', $identifier)
        ->where('cps.store_id IN (?)', $store);

        if ($isActive !== null) {
            $select->where('cp.is_active IN(?)', $isActive);
        }

        return $select;
    }

    /**
     * @param AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @throws \Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table = $this->getTable('amasty_xlanding_page_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['page_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = ['page_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        if ($this->moduleManager->isEnabled('Magento_UrlRewrite')) {
            $urls = array_search('0', $newStores) === false ? $this->generateForSpecificStores($object, $newStores)
                : $this->generateForAllStores($object);
            $this->urlPersist->deleteByData(
                [
                    UrlRewrite::ENTITY_ID => $object->getId(),
                    UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE,
                ]
            );
            $this->urlPersist->replace($urls);
        }

        return parent::_afterSave($object);
    }

    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['page_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable('amasty_xlanding_page_store'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * @param AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->urlPersist->deleteByData(
            [
                UrlRewrite::ENTITY_ID => $object->getId(),
                UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE,
            ]
        );

        return parent::_afterDelete($object);
    }

    /**
     * @param int $storeId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function getSitemapCollection($storeId)
    {
        $pages = [];

        $select = $this->getConnection()->select()->from(
            ['main_table' => $this->getMainTable()],
            [$this->getIdFieldName(), 'url' => 'identifier', 'updated_at' => 'update_time']
        )->join(
            ['store_table' => $this->getTable('amasty_xlanding_page_store')],
            'main_table.page_id = store_table.page_id',
            []
        )->where(
            'main_table.is_active = ?',
            PageModel::STATUS_ENABLED
        )->where(
            'store_table.store_id IN(?)',
            [0, $storeId]
        );

        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $page = $this->_prepareObject($row);
            $pages[$page->getId()] = $page;
        }

        return $pages;
    }

    /**
     * @param int $categoryId
     * @param null $dynamicPageId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function syncDynamicPages($categoryId, $dynamicPageId = null)
    {
        $where =  PageInterface::LANDING_IS_ACTIVE . ' = ' . PageModel::STATUS_DYNAMIC . ' AND '
            . PageInterface::DYNAMIC_CATEGORY_ID . ' = ' . $categoryId;
        if ($dynamicPageId) {
            $where .= ' AND ' . PageInterface::LANDING_PAGE_ID . ' != ' . $dynamicPageId;
        }
        $bind = [PageInterface::LANDING_IS_ACTIVE => PageModel::STATUS_DISABLED];

        $this->getConnection()->update($this->getMainTable(), $bind, $where);

        if ($dynamicPageId) {
            $bind = [
                PageInterface::LANDING_IS_ACTIVE => PageModel::STATUS_DYNAMIC,
                PageInterface::DYNAMIC_CATEGORY_ID => $categoryId,
            ];
            $where = PageInterface::LANDING_PAGE_ID . ' = ' . $dynamicPageId;
            $this->getConnection()->update($this->getMainTable(), $bind, $where);
        }
    }

    /**
     * @param array $data
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareObject(array $data)
    {
        $object = new \Magento\Framework\DataObject();
        $object->setId($data[$this->getIdFieldName()]);
        $object->setUrl($data['url']);
        $object->setUpdatedAt($data['updated_at']);

        return $object;
    }

    /**
     * @param PageModel $page
     * @param bool $useIndex
     * @return array
     */
    public function getProductPositionData(PageModel $page, $useIndex = false)
    {
        $connection = $this->getConnection();
        $table = 'amasty_xlanding_page_product';
        if ($useIndex) {
            $table = 'amasty_xlanding_page_product_index';
        }
        $storePositionData = [];
        $select = $connection->select()->from(
            ['main_table' => $this->getTable($table)],
            ['product_id', 'position', 'store_id']
        )->where('page_id = ?', $page->getId());
        $positionData = $connection->fetchAll($select);

        foreach ($positionData as $item) {
            $storePositionData[$item['store_id']][$item['product_id']] = $item['position'];
        }

        return $storePositionData;
    }

    /**
     * @param PageModel $page
     * @return $this
     */
    public function saveProductPositionData(PageModel $page)
    {
        $allPositionData = $page->getProductPositionData();
        if (!empty($allPositionData)) {
            $connection = $this->getConnection();
            $connection->delete(
                $this->getTable('amasty_xlanding_page_product'),
                ['page_id = ?' => $page->getId()]
            );

            $insertData = [];
            foreach ($allPositionData as $storeId => $positionData) {
                if (empty($positionData)) {
                    continue;
                }
                foreach ($positionData as $productId => $position) {
                    $insertData[] = [
                        'page_id' => $page->getId(),
                        'product_id' => $productId,
                        'store_id'  => $storeId,
                        'position'  => $position
                    ];
                }
            }
            if (!empty($insertData)) {
                $connection->insertOnDuplicate($this->getTable('amasty_xlanding_page_product'), $insertData);
            }
        }
        return $this;
    }

    /**
     * @param mixed $productIds
     * @param null $storeId
     * @return array
     */
    public function getIndexPageIdsByProductId($productIds, $storeId = null)
    {
        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }
        $connection = $this->getConnection();
        $table = 'amasty_xlanding_page_product_index';
        $select = $connection->select()->from(
            ['main_table' => $this->getTable($table)],
            [
                'product_id',
                'page_ids'=> new \Zend_Db_Expr("GROUP_CONCAT(page_id separator ',')"),
                'positions'=> new \Zend_Db_Expr("GROUP_CONCAT(position separator ',')")
            ]
        )->where('store_id = ?', $storeId)
            ->where('product_id IN (?)', $productIds)
            ->group('product_id');
        $landingPositionData = $connection->fetchAll($select);
        $positionData = [];
        foreach ($landingPositionData as $data) {
            $positionData[$data['product_id']] = array_combine(
                explode(',', $data['page_ids']),
                explode(',', $data['positions'])
            );
        }

        return $positionData;
    }
}
