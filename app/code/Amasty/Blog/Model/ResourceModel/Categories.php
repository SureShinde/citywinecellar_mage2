<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Exception\AlreadyExistsException;
use Amasty\Blog\Model\Source\CategoryStatus;
use Amasty\Blog\Api\Data\CategoryInterface;
use Amasty\Blog\Model\ResourceModel\Traits\ResourceModelTrait;

class Categories extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    use ResourceModelTrait;

    const TABLE_NAME = 'amasty_blog_categories';

    const STORE_TABLE_NAME = 'amasty_blog_categories_store';

    const STORE_TABLE_FIELDS = [
        CategoryInterface::CATEGORY_ID,
        CategoryInterface::STORE_ID,
        CategoryInterface::NAME,
        CategoryInterface::STATUS,
        CategoryInterface::META_TITLE,
        CategoryInterface::META_DESCRIPTION,
        CategoryInterface::META_TAGS,
    ];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Amasty\Blog\Helper\Data
     */
    private $helper;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Blog\Helper\Data $helper,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->storeManager = $storeManager;
        $this->helper = $helper;
    }

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, 'category_id');
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveStoreData($object);

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        try {
            $this->validateCategory($object);
        } catch (AlreadyExistsException $e) {
            throw new LocalizedException(__($e->getMessage()));
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     *
     * @throws AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateCategory(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$this->validateUrlKey($object) && ($object->getStatus() != CategoryStatus::STATUS_DISABLED)) {
            $object->setStatus(CategoryStatus::STATUS_DISABLED);

            throw new AlreadyExistsException(
                __(
                    "Category '%1' can be disabled only. Some category has same Url Key for the same Store View.",
                    $object->getTitle()
                )
            );
        }
    }

    /**
     * @param $object
     * @return bool
     * @throws AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateUrlKey($object)
    {
        $store = $object->getStores();
        $store = is_array($store) ? $store : [$store];

        $connection = $this->getConnection();
        $bind = ['url_key' => $object->getUrlKey()];

        $select = $connection->select()->from(
            ['main_table' => $this->getMainTable()],
            [$this->getIdFieldName()]
        )->joinLeft(
            ['store' => $this->getTable('amasty_blog_categories_store')],
            'main_table.category_id = store.category_id',
            ['store.store_id']
        )->where(
            'main_table.url_key = :url_key'
        );
        $id = (int)$object->getId();
        if ($id) {
            $bind['category_id'] = $id;
            $select->where('store.category_id != :category_id');
        }

        $bind['store_id'] = implode(', ', $store);
        $select->where('store.store_id IN (:store_id)');

        $result = $connection->fetchOne($select, $bind);
        if ($result !== false) {
            throw new AlreadyExistsException(
                __('Category with the same url key already exists.')
            );
        }

        return true;
    }

    /**
     * @param AbstractModel $object
     * @return $this|\Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->addDefaultStoreSelect($object);

        return $this;
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getStores($id)
    {
        $select = $this->getConnection()->select()
            ->from(
                [$this->getTable('amasty_blog_categories_store')],
                ['category_id', 'store_id']
            )
            ->where('category_id = :category_id');

        return $this->getConnection()->fetchAssoc($select, [':category_id' => $id]);
    }
}
