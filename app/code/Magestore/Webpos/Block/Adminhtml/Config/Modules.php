<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Block\Adminhtml\Config;

class Modules extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magestore_Webpos::config/modules.phtml';

    /**
     * @var \Magento\Framework\Module\FullModuleList
     */
    protected $moduleList;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\FullModuleList $moduleList,
        array $data = []
    ) {
        $this->moduleList = $moduleList;
        parent::__construct($context, $data);
    }

    /**
     * Get list of modules and versions
     *
     * @return string[]
     */
    public function getModules()
    {
        $result = [];
        foreach ($this->moduleList->getAll() as $name => $module) {
            if (0 === strpos($name, 'Magestore_')) {
                $result[substr($name, 10)] = $module['setup_version'];
            }
        }
        return $result;
    }
}
