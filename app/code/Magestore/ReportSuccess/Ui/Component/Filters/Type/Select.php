<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Ui\Component\Filters\Type;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Ui\Component\Filters\FilterModifier;

/**
 * Class Select
 * @package Magestore\ReportSuccess\Ui\Component\Filters\Type
 */
class Select extends \Magento\Ui\Component\Filters\Type\Select
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        FilterModifier $filterModifier,
        OptionSourceInterface $optionsProvider = null,
        array $components = [],
        array $data = [],
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $uiComponentFactory, $filterBuilder, $filterModifier, $optionsProvider, $components, $data);
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->moduleManager->isEnabled('Magestore_SupplierSuccess')){
            $this->_data['config']['componentDisabled'] = true;
        } else {
            parent::prepare();
        }
    }
}
