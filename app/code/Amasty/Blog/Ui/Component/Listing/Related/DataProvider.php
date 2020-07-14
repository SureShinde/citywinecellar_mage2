<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Ui\Component\Listing\Related;

use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * Class
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @param SearchResultInterface $searchResult
     *
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [];
        $arrItems['items'] = [];
        $this->applyFilters($searchResult);

        foreach ($searchResult->getItems() as $item) {
            $data = $item->getData();
            if (isset($data['stores']) && $data['stores'] == '0') {
                $data['stores'] = ['0'];
            }
            $arrItems['items'][] = $data;
        }

        $arrItems['totalRecords'] = $searchResult->getSize();

        return $arrItems;
    }

    protected function applyFilters(SearchResultInterface $searchResult)
    {
        $tag = (int)$this->request->getParam('tag_id');
        if ($tag) {
            $searchResult->addTagFilter($tag);
        } else {
            $category = (int)$this->request->getParam('category_id');
            if ($category) {
                $searchResult->addCategoryFilter([$category]);
            } else {
                $author = (int)$this->request->getParam('author_id');
                $searchResult->addAuthorFilter([$author]);
            }
        }
    }
}
