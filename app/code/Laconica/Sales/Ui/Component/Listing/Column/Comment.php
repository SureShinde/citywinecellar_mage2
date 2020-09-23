<?php

namespace Laconica\Sales\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Comment extends \Magento\Ui\Component\Listing\Columns\Column
{
    const FILTER_COMMENTS_PATH = 'la_sales_grid/general/filter_list';

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    private $filterOutStrings = [];

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
        $this->filterOutStrings = $this->setFilterArray();
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (!isset($item["entity_id"]) || !$item["entity_id"]) {
                    continue;
                }
                $order = $this->orderRepository->get($item["entity_id"]);
                if (!$order || !$order->getId()) {
                    continue;
                }
                $orderComments = $order->getAllStatusHistory();
                $item[$this->getData('name')] = $this->renderComments($orderComments);
            }
        }
        return $dataSource;
    }

    /**
     * Logic took from m1 website
     *
     * @param array $orderComments
     * @return string
     */
    private function renderComments(array $orderComments)
    {
        $result = [];
        foreach ($orderComments as $comment) {
            $body = $comment->getData('comment');
            $systemStrMatch = false;
            foreach ($this->filterOutStrings as $filterStr) {
                if (strpos($body, $filterStr) !== false) {
                    $systemStrMatch = true;
                    break;
                }
            }

            if (!empty($body) && (!$systemStrMatch)) {
                $result[] = '- ' . $body;
            }
        }

        return implode("<br/><br/>", $result);
    }

    /**
     * Set Comment Filter params
     *
     * @return array
     */
    private function setFilterArray()
    {
        $filterString = $this->scopeConfig->getValue(self::FILTER_COMMENTS_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return array_map('trim', explode(',', $filterString));
    }
}