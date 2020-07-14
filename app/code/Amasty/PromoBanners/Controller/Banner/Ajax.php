<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Controller\Banner;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultFactory;

class Ajax extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Amasty\PromoBanners\Model\Banner\Data
     */
    private $dataSource;

    public function __construct(
        Context $context,
        \Amasty\PromoBanners\Model\Banner\Data $dataSource
    ) {
        parent::__construct($context);
        $this->dataSource = $dataSource;
    }

    public function execute()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
            $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);

            return $resultRaw->setHttpResponseCode(400);
        }

        // Required for proper render of product listings
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $sections = $this->_request->getParam('sections', []);
        $context = $this->_request->getParam('context', []);
        $bannerIds = $this->_request->getParam('banners', []);

        // Init default values
        $context += [
            'currentProduct' => null,
            'currentCategory' => null,
            'searchQuery' => null,
        ];

        $productSku = $context['currentProduct'];
        $categoryId = ((int)$context['currentCategory']) ?: null;
        $searchQuery = $context['searchQuery'];

        $response = $this->dataSource->getBanners(
            $sections,
            $categoryId,
            $productSku,
            $searchQuery,
            $bannerIds
        );

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $resultJson->setData($response);
    }
}
