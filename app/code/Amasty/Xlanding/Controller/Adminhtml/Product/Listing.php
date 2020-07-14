<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Controller\Adminhtml\Product;

use Magento\Framework\Exception\NotFoundException;

class Listing extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Amasty_Xlanding::page';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    private $pageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Registry $registry,
        \Amasty\Xlanding\Model\PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->registry = $registry;
        $this->pageFactory = $pageFactory;
    }

    private function initPage()
    {
        $pageId = $this->getRequest()->getParam('page_id', false);
        $model = $this->pageFactory->create();
        if ($pageId) {
            $model->load($pageId);
        }

        $this->registry->register('amasty_xlanding_page', $model);

        return $model;
    }

    /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return \Magento\Framework\Controller\Result\Raw
     * @throws NotFoundException
     */
    public function execute()
    {
        $this->initPage();

        $block = $this->layoutFactory->create()->createBlock(
            \Amasty\Xlanding\Block\Adminhtml\Page\Edit\Tab\Product\Listing::class,
            'product.listing'
        );

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $block->toHtml()
        );
    }

}
