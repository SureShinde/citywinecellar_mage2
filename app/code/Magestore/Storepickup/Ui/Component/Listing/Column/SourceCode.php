<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storepickup
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storepickup\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class SourceCode
 * @package Magestore\Storepickup\Ui\Component\Listing\Column
 */
class SourceCode extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magestore\Storepickup\Helper\Data
     */
    protected $storePickupHelper;

    /**
     * Constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magestore\Storepickup\Helper\Data $storePickupHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storePickupHelper = $storePickupHelper;
    }
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->storePickupHelper->isMSISourceEnable()) {
            $config = $this->getData('config');
            $config['componentDisabled'] = true;
            $this->setData('config', $config);
        }
    }
}
