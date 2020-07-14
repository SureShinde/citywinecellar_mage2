<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Ui\Component\Listing\Columns;

/**
 * Class Categories
 * @package Magestore\ReportSuccess\Ui\Component\Listing\Columns
 */
class Categories extends Scroll
{

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $filterParam = $this->context->getFilterParam('category_ids');
        if(isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $categoryNameArray = [];
                if ($item[$fieldName]) {
                    foreach ($item[$fieldName] as $categoryId) {
                        $categoryModel = $this->categoryFactory->create()->load($categoryId);
                        if (!$filterParam || ($filterParam && in_array($categoryId, $filterParam))) {
                            $categoryNameArray[] = $categoryModel->getName();
                        }
                    }
                    $item[$fieldName] = $this->addScrollToField(implode('</br>', $categoryNameArray));
                }
            }
        }
        return $dataSource;
    }
}
