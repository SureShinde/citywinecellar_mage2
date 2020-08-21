<?php
/**
 *  Copyright © Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Model\Report\PanelItems;

use Magestore\ReportSuccess\Api\Data\ReportPanelItemInterface;

/**
 * Class AbstractItem
 *
 * Used to create Abstract Item
 */
class AbstractItem extends \Magento\Framework\DataObject implements ReportPanelItemInterface
{

    /**
     * AbstractItem constructor.
     *
     * @param string $id
     * @param string $title
     * @param string $description
     * @param string $action
     * @param bool $isVisible
     * @param int $sortOrder
     */
    public function __construct(
        $id,
        $title,
        $description,
        $action,
        $isVisible = true,
        $sortOrder = 0
    ) {
        parent::__construct(
            [
               self::ITEM_ID => $id,
               self::TITLE => $title,
               self::DESCRIPTION => $description,
               self::ACTION => $action,
               self::IS_VISIBLE => $isVisible,
               self::SORT_ORDER => $sortOrder
            ]
        );
        $this->modifyVisible();
    }

    /**
     * @inheritdoc
     * */
    public function getId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * @inheritdoc
     * */
    public function setId($value)
    {
        return $this->setData(self::ITEM_ID, $value);
    }

    /**
     * @inheritdoc
     * */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritdoc
     * */
    public function setTitle($value)
    {
        return $this->setData(self::TITLE, $value);
    }

    /**
     * @inheritdoc
     * */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritdoc
     * */
    public function setDescription($value)
    {
        return $this->setData(self::DESCRIPTION, $value);
    }

    /**
     * @inheritdoc
     * */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * @inheritdoc
     * */
    public function setAction($value)
    {
        return $this->setData(self::ACTION, $value);
    }

    /**
     * @inheritdoc
     * */
    public function getIsVisible()
    {
        return $this->getData(self::IS_VISIBLE);
    }

    /**
     * @inheritdoc
     * */
    public function setIsViSible($value)
    {
        return $this->setData(self::IS_VISIBLE, $value);
    }

    /**
     * @inheritdoc
     * */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @inheritdoc
     * */
    public function setSortOrder($value)
    {
        return $this->setData(self::SORT_ORDER, $value);
    }

    /**
     * Modify module visibility since it depend on other modules
     *
     * @return $this
     * */
    public function modifyVisible()
    {
        return $this;
    }
}
