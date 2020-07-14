<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */

namespace Amasty\Xlanding\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;

class PageActions extends Column
{
    const LANDING_URL_PATH_EDIT = 'amasty_xlanding/page/edit';
    const LANDING_URL_PATH_DELETE = 'amasty_xlanding/page/delete';
    const LANDING_URL_PATH_DUPLICATE = 'amasty_xlanding/page/duplicate';

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @var UrlBuilder
     */
    private $actionUrlBuilder;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ContextInterface $context,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        UiComponentFactory $uiComponentFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = [],
        $editUrl = self::LANDING_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->editUrl = $editUrl;
        $this->storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dSource
     * @return array
     */
    public function prepareDataSource(array $dSource)
    {
        if (isset($dSource['data']['items'])) {
            foreach ($dSource['data']['items'] as & $item) {
                $stores = $this->storeManager->getStores();
                $store = array_shift($stores);
                $name = $this->getData('name');
                if (isset($item['identifier'])) {
                    $item[$name]['preview'] = [
                        'href' => $this->actionUrlBuilder->getUrl(
                            $item['identifier'],
                            $store->getId(),
                            $store->getCode()
                        ),
                        'label' => __('Preview')
                    ];
                }
                if (isset($item['page_id'])) {
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::LANDING_URL_PATH_DELETE,
                            ['page_id' => $item['page_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete ${ $.$data.title }'),
                            'message' => __('Are you sure you wan\'t to delete a ${ $.$data.title } record?')
                        ]
                    ];
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->editUrl, ['page_id' => $item['page_id']]),
                        'label' => __('Edit')
                    ];

                    $item[$name]['duplicate'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::LANDING_URL_PATH_DUPLICATE,
                            ['page_id' => $item['page_id']]
                        ),
                        'label' => __('Duplicate')
                    ];
                }
            }
        }

        return $dSource;
    }
}
