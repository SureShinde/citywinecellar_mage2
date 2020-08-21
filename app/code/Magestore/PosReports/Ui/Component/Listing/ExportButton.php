<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Ui\Component\Listing;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;

/**
 * Class ExportButton
 *
 * Used to create Export button
 */
class ExportButton extends \Magento\Ui\Component\ExportButton
{

    /**
     * @var string[]
     */
    protected $allowedFileTypes;

    /**
     * ExportButton constructor.
     *
     * @param ContextInterface $context
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param array $allowedFileTypes
     */
    public function __construct(
        ContextInterface $context,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        array $allowedFileTypes = []
    ) {
        $this->allowedFileTypes = $allowedFileTypes;
        parent::__construct($context, $urlBuilder, $components, $data);
    }

    /**
     * Prepare
     *
     * @return void
     */
    public function prepare()
    {
        $context = $this->getContext();
        $config = $this->getData('config');
        if (isset($config['options'])) {
            $options = [];
            foreach ($config['options'] as $option) {
                if (isset($option['value']) && in_array($option['value'], $this->allowedFileTypes)) {
                    $additionalParams = $this->getAdditionalParams($config, $context);
                    $option['url'] = $this->urlBuilder->getUrl($option['url'], $additionalParams);
                    $options[] = $option;
                }
            }
            $config['options'] = $options;
            $this->setData('config', $config);
        }
        parent::prepare();
    }

    /**
     * Get export button additional parameters
     *
     * @param array $config
     * @param ContextInterface $context
     * @return array
     */
    protected function getAdditionalParams($config, $context)
    {
        $additionalParams = [];
        if (isset($config['additionalParams'])) {
            foreach ($config['additionalParams'] as $paramName => $paramValue) {
                if (substr_compare($paramValue, "$", 0, strlen("$")) === 0) {
                    continue;
                }
                if ('*' == $paramValue) {
                    $paramValue = $context->getRequestParam($paramName);
                }
                $additionalParams[$paramName] = $paramValue;
            }
        }
        return $additionalParams;
    }
}
