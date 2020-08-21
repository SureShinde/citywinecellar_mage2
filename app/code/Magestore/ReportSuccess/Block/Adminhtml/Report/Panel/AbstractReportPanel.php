<?php
/**
 *  Copyright © Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Block\Adminhtml\Report\Panel;

use Magento\Framework\View\Element\Template;
use Magestore\ReportSuccess\Api\Data\ReportPanelItemInterface;

/**
 * Class AbstractReportPanel
 *
 * Used to create Abstract Report Panel
 */
class AbstractReportPanel extends \Magento\Framework\View\Element\Template
{
    /**
     * @var array
     * */
    protected $_panelItems;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Framework\AuthorizationInterface $authorization
     * */
    protected $_authorization;

    /**
     * AbstractReportPanel constructor.
     *
     * @param Template\Context $context
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param array $data
     * @param array $panelItems
     *
     * @throws \Exception
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\AuthorizationInterface $authorization,
        array $data = [],
        array $panelItems = []
    ) {
        parent::__construct($context, $data);
        $this->_moduleManager = $moduleManager;
        $this->_authorization = $authorization;
        $this->initPanelItems($panelItems);
    }

    /**
     * Init Panel Items
     *
     * @param array $panelItems
     *
     * @return $this
     * @throws \Exception
     * */
    protected function initPanelItems($panelItems)
    {
        foreach ($panelItems as $panelItemId => $panelItem) {
            if ($panelItem instanceof ReportPanelItemInterface) {
                $this->_panelItems[$panelItemId] = $panelItem;
            }
        }
        return $this->sortPanelItems();
    }

    /**
     * Sort Panel Item by sortOrder
     *
     * @return $this
     * */
    private function sortPanelItems()
    {
        $panelItem = $this->_panelItems;

        uasort(
            $panelItem,
            function (ReportPanelItemInterface $a, ReportPanelItemInterface $b) {
                return $a->getSortOrder() <=> $b->getSortOrder();
            }
        );

        $this->_panelItems = $panelItem;

        return $this;
    }

    const ADMIN_RESOURCE = '';

    /**
     * @var string
     */
    protected $_panelHeadingTitle = '';

    /**
     * @var string
     */
    protected $_template = 'Magestore_ReportSuccess::report/panel.phtml';

    /**
     * Get Heading Title
     *
     * @return string
     * */
    public function getHeadingTitle()
    {
        return $this->_panelHeadingTitle;
    }

    /**
     * Retrieved all Panel Items
     *
     * @return ReportPanelItemInterface[];
     * */
    public function getPanelItems()
    {
        return $this->_panelItems;
    }

    /**
     * Retrieved visible Panel Items
     *
     * @return ReportPanelItemInterface[];
     * */
    public function getPanelItemVisible()
    {
        return $this->getPanelItems();
    }

    /**
     * Is Allowed
     *
     * @param string $permission
     * @return bool
     */
    public function isAllowed($permission)
    {
        return $this->_authorization->isAllowed($permission);
    }

    /**
     * Get report link from give path
     *
     * @param string $path
     * @return string
     */
    public function getActionUrl($path)
    {
        return $this->getUrl($path, ['_forced_secure' => $this->getRequest()->isSecure()]);
    }

    /**
     * Is Visible
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->isAllowed(static::ADMIN_RESOURCE);
    }
}
