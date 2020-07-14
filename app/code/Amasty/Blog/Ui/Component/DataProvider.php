<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Ui\Component;

use Magento\Framework\Api\Search\SearchResultInterface;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    const POSSIBLE_ZERO_VALUES = ['stores', 'store_id'];

    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [];

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $arrItems['items'][] = $this->prepareZeroValues($item);
        }

        $arrItems['totalRecords'] = $searchResult->getSize();

        return $arrItems;
    }

    /**
     * @param $item
     * @return array
     */
    private function prepareZeroValues($item)
    {
        $data = $item->getData();

        foreach (self::POSSIBLE_ZERO_VALUES as $value) {
            if (isset($data[$value]) && $data[$value] == '0') {
                $data[$value] = [$data[$value]];
            }
        }

        return $data;
    }
}
