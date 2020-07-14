<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Ui\Component\Form\Post;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;

/**
 * Class AbstractChangeLinkUrl
 */
class AbstractChangeLinkUrl extends Field
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        if (isset($config['tooltip']['link'])) {
            $config['tooltip']['link'] = $this->urlBuilder->getUrl($config['tooltip']['link']);
        }
        $this->setData('config', $config);
    }
}
