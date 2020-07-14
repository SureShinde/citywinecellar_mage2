<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

/**
 * Class PostActions
 */
class PostActions extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $item[$name]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'amasty_blog/posts/edit',
                        ['id' => $item['post_id']]
                    ),
                    'label' => __('Edit')
                ];
                $item[$name]['preview'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'amasty_blog/posts/preview',
                        ['id' => $item['post_id']]
                    ),
                    'target' => '_blank',
                    'label' => __('Preview')
                ];
                $item[$name]['delete'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'amasty_blog/posts/duplicate',
                        ['id' => $item['post_id']]
                    ),
                    'label' => __('Duplicate'),
                    'confirm' => [
                        'title' => __('Duplicate "${ $.$data.title }"'),
                        'message' => __('Are you sure you want to duplicate a "${ $.$data.title }" record?')
                    ]
                ];
            }
        }

        return $dataSource;
    }
}
