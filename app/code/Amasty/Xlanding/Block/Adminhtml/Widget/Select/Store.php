<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Block\Adminhtml\Widget\Select;

use Amasty\Xlanding\Model\Page\Product\AdminhtmlDataProvider;

class Store extends \Magento\Backend\Block\Widget
{
    /**
     * @var AdminhtmlDataProvider
     */
    private $dataProvider;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    private $systemStore;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\Xlanding\Model\Page\Product\AdminhtmlDataProvider $dataProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataProvider = $dataProvider;
    }

    /**
     * Define block template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/select.phtml');
    }

    /**
     * @return array
     */
    public function getSelectOptions()
    {
        $options = [];
        foreach ($this->_storeManager->getWebsites() as $website) {
            foreach ($website->getStores() as $store) {
                $options[$website->getName()][$store->getId()] = $store->getName();
            }
        }

        return $options;
    }

    /**
     * Get current value
     *
     * @return string
     */
    public function getSelectValue()
    {
        return $this->dataProvider->getStoreId();
    }
}
