<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Block\Adminhtml\Widget\Select;

use Amasty\Xlanding\Model\Page\Product\AdminhtmlDataProvider;

class SortOrder extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Amasty\Xlanding\Model\Page\Product\Sorting
     */
    private $sorting;

    /**
     * @var AdminhtmlDataProvider
     */
    private $dataProvider;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\Xlanding\Model\Page\Product\Sorting $sorting,
        \Amasty\Xlanding\Model\Page\Product\AdminhtmlDataProvider $dataProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sorting = $sorting;
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
        return $this->sorting->getSortingOptions();
    }

    /**
     * Get current value
     *
     * @return string
     */
    public function getSelectValue()
    {
        return $this->dataProvider->getSortOrder();
    }
}
