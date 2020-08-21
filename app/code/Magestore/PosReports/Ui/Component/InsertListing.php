<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Ui\Component;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;

/**
 * Class InsertListing
 *
 * Used to create Insert Listing
 */
class InsertListing extends \Magento\Ui\Component\AbstractComponent
{
    const NAME = "insertListing";

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * InsertListing constructor.
     *
     * @param ContextInterface $context
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $components, $data);
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return self::NAME;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        if (isset($config['submit_url'])) {
            $config['submit_url'] = $this->_urlBuilder->getUrl($config['submit_url']);
        }
        $this->setData('config', (array)$config);
    }
}
