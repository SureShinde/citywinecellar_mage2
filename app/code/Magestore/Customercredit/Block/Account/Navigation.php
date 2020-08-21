<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

namespace Magestore\Customercredit\Block\Account;

/**
 * Class Navigation
 *
 * Acount navigation block
 */
class Navigation extends \Magento\Framework\View\Element\Template
{
    protected $_navigationTitle = '';
    protected $_links = [];

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_requestInterface;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magestore\Customercredit\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Navigation constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magestore\Customercredit\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Customercredit\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_requestInterface = $context->getRequest();
        $this->_storeManager = $context->getStoreManager();
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * Set Navigation Title
     *
     * @param string $title
     * @return $this
     */
    public function setNavigationTitle($title)
    {
        $this->_navigationTitle = $title;
        return $this;
    }

    /**
     * Get Navigation Title
     *
     * @return string
     */
    public function getNavigationTitle()
    {
        return $this->_navigationTitle;
    }

    /**
     * Get Store zManager
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    /**
     * Get Helper
     *
     * @return \Magestore\Customercredit\Helper\Data
     */
    public function getHelper()
    {
        return $this->_dataHelper;
    }

    /**
     * Add Link
     *
     * @param string $name
     * @param string $path
     * @param string $label
     * @param bool $disabled
     * @param int $order
     * @param array $urlParams
     * @return $this
     */
    public function addLink($name, $path, $label, $disabled = false, $order = 0, $urlParams = [])
    {
        if (isset($this->_links[$order])) {
            $order++;
        }

        $link = new \Magento\Framework\DataObject(
            [
                'name' => $name,
                'path' => $path,
                'label' => $label,
                'disabled' => $disabled,
                'order' => $order,
                'url' => $this->getUrl($path, $urlParams),
            ]
        );

        $this->_eventManager->dispatch(
            'customercredit_account_navigation_add_link',
            [
                'block' => $this,
                'link' => $link,
            ]
        );

        $this->_links[$order] = $link;
        return $this;
    }

    /**
     * Get Links
     *
     * @return array
     */
    public function getLinks()
    {
        $links = new \Magento\Framework\DataObject(
            [
                'links' => $this->_links,
            ]
        );

        $this->_eventManager->dispatch(
            'customercredit_account_navigation_get_links',
            [
                'block' => $this,
                'links_obj' => $links,
            ]
        );

        $this->_links = $links->getLinks();

        ksort($this->_links);

        return $this->_links;
    }

    /**
     * Is Active
     *
     * @param \Magento\Framework\DataObject $link
     * @return bool
     */
    public function isActive($link)
    {
        $aciveLink = $this->_requestInterface->getFullActionName("/");
        if ($aciveLink == $link->getPath()) {
            return true;
        }
        return false;
    }
}
