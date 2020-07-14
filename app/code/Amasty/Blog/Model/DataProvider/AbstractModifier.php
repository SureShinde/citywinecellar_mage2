<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\DataProvider;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class AbstractModifier implements ModifierInterface
{
    const CURRENT_STORE_ID = 'amasty_blog_store_id';

    /**
     * @var \Amasty\Blog\Model\BlogRegistry
     */
    private $blogRegistry;

    /**
     * @var string
     */
    private $currentEntityKey;

    /**
     * @var array
     */
    private $fieldsByStore;

    /**
     * @var mixed
     */
    private $repository;

    public function __construct(
        \Amasty\Blog\Model\BlogRegistry $blogRegistry,
        $currentEntityKey = '',
        $fieldsByStore = [],
        array $data = []
    ) {
        $this->blogRegistry = $blogRegistry;
        $this->currentEntityKey = $currentEntityKey;
        $this->fieldsByStore = $fieldsByStore;
        $this->repository = $data['repository'];
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $storeId = (int)$this->blogRegistry->registry(self::CURRENT_STORE_ID);
        if ($storeId) {
            $item = $this->blogRegistry->registry($this->currentEntityKey);
            if ($item) {
                $itemId = $item->getId();
                $this->changeFields($itemId, $storeId, $meta);
            }
        }

        return $meta;
    }

    /**
     * @param int $itemId
     * @param int $storeId
     * @param array $meta
     */
    private function changeFields($itemId, $storeId, &$meta)
    {
        $item = $this->repository->getByIdAndStore($itemId, $storeId, false);
        foreach ($this->fieldsByStore as $group => $fieldSet) {
            foreach ($fieldSet as $field) {
                $meta[$group]['children'][$field]['arguments']['data']['config']['service'] =
                    ['template' => 'ui/form/element/helper/service'];
                if ($item->getData($field) === null) {
                    $meta[$group]['children'][$field]['arguments']['data']['config']['disabled'] = true;
                }
            }
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
