<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Ui\Component\Listing\Columns\Shipment;

use Magento\Ui\Component\Control\Action;

/**
 * Class PdfAction
 */
class PdfAction extends Action
{
    /**
     * Prepare
     * 
     * @return void
     */
    public function prepare()
    {
        $config = $this->getConfiguration();
        $context = $this->getContext();
        $config['url'] = $context->getUrl(
            $config['pdfAction'],
            ['id' => $context->getRequestParam('id')]
        );
        $this->setData('config', (array)$config);
        parent::prepare();
    }
}
