<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Ui\Component\Listing\Columns;

/**
 * Class Location
 * @package Magestore\ReportSuccess\Ui\Component\Listing\Columns
 */
class ResourceName extends Scroll
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepare()
    {
        parent::prepare();
        $data = $this->getData();
        if ($data && $data['config']) {
            $data['config']['label'] = __('Source');
        }
        $this->setData($data);
    }
}
