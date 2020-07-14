<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Ui\Component;
/**
 * Class ExportButton
 * @package Magestore\ReportSuccess\Ui\Component
 */
class ExportButton extends \Magento\Ui\Component\ExportButton
{
    private $removeOption = 'xml';
    /**
     * @return void
     */
    public function prepare()
    {
        $config = $this->getData('config');

        if (isset($config['options'])) {
            $options = [];
            if(isset($config['options'][$this->removeOption])) {
                unset($config['options'][$this->removeOption]);
            }

            foreach ($config['options'] as $option) {
                $option['url'] = $this->urlBuilder->getUrl($option['url']);
                $options[] = $option;
            }
            $config['options'] = $options;
            $this->setData('config', $config);
        }
        parent::prepare();
    }
}
