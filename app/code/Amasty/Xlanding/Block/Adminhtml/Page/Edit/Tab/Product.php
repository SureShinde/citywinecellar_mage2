<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Block\Adminhtml\Page\Edit\Tab;

class Product extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var Product\Listing
     */
    private $listingBlock;

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListingBlock()
    {
        if (null === $this->listingBlock) {
            $this->listingBlock = $this->getLayout()->getBlock(
                'amasty_xlanding_page_edit_tab_product_listing'
            );
        }
        return $this->listingBlock;
    }
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Page Products');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Page Products');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * @return string
     */
    public function getSavePositionsUrl()
    {
        return $this->getUrl('amasty_xlanding/product/save');
    }

    /**
     * @return mixed
     */
    public function getPageId()
    {
        return $this->getRequest()->getParam('page_id');
    }

    /**
     * @return string
     */
    public function getPositionDataJson()
    {
        return \Zend_Json::encode([]);
    }
}
