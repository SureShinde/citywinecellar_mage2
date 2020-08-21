<?php
/**
 *  Copyright © Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Model\Report\PanelItems;

/**
 * Class IncomingStock
 *
 * Used to create Incoming Stock
 */
class IncomingStock extends AbstractItem
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * IncomingStock constructor.
     *
     * @param string $id
     * @param string $title
     * @param string $description
     * @param string $action
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param bool $isVisible
     * @param int $sortOrder
     */
    public function __construct(
        $id,
        $title,
        $description,
        $action,
        \Magento\Framework\Module\Manager $moduleManager,
        $isVisible = true,
        $sortOrder = 0
    ) {
        $this->_moduleManager = $moduleManager;
        parent::__construct($id, $title, $description, $action, $isVisible, $sortOrder);
    }

    /**
     * @inheritdoc
     * */
    public function modifyVisible()
    {
        if (!$this->_moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')) {
            $this->setIsViSible(false);
        }

        return $this;
    }
}
