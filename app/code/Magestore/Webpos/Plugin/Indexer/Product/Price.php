<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Plugin\Indexer\Product;

/**
 * Class Price
 *
 * Used to plugin price indexer
 */
class Price
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Price constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * Before execute
     *
     * @param \Magento\Catalog\Model\Indexer\Product\Price $subject
     * @param array $ids
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(
        \Magento\Catalog\Model\Indexer\Product\Price $subject,
        $ids
    ) {
        $this->request->setParam('skipPluginHideCustomSaleType', true);
    }

    /**
     * Before execute full
     *
     * @param \Magento\Catalog\Model\Indexer\Product\Price $subject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecuteFull(
        \Magento\Catalog\Model\Indexer\Product\Price $subject
    ) {
        $this->request->setParam('skipPluginHideCustomSaleType', true);
    }

    /**
     * Before execute list
     *
     * @param \Magento\Catalog\Model\Indexer\Product\Price $subject
     * @param array $ids
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecuteList(
        \Magento\Catalog\Model\Indexer\Product\Price $subject,
        $ids
    ) {
        $this->request->setParam('skipPluginHideCustomSaleType', true);
    }

    /**
     * Before execute row
     *
     * @param \Magento\Catalog\Model\Indexer\Product\Price $subject
     * @param int $id
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecuteRow(
        \Magento\Catalog\Model\Indexer\Product\Price $subject,
        $id
    ) {
        $this->request->setParam('skipPluginHideCustomSaleType', true);
    }
}
