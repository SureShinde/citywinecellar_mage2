<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposTracking\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Framework\App\Cache\TypeListInterface;

/**
 * Class AbstractConfig
 *
 * @package Magestore\WebposTracking\Controller\Adminhtml\Config
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class AbstractConfig extends \Magento\Backend\App\Action
{
    /**
     * @var \Magestore\WebposTracking\Helper\Data
     */
    protected $helper;
    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * AbstractConfig constructor.
     *
     * @param Action\Context $context
     * @param \Magestore\WebposTracking\Helper\Data $helper
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Action\Context $context,
        \Magestore\WebposTracking\Helper\Data $helper,
        TypeListInterface $cacheTypeList
    ) {
        $this->helper = $helper;
        $this->cacheTypeList = $cacheTypeList;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->helper->setConfig(\Magestore\WebposTracking\Model\Service\TrackingService::IS_SHOW_NOTIFICATION_PATH, 0);
        $this->cacheTypeList->cleanType(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
    }
}
