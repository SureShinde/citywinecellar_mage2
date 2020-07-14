<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Layout;

/**
 * Class
 */
class Container extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Blog\Helper\Data\Layout
     */
    private $helperLayout;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $objectFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Blog\Helper\Data\Layout $helperLayout,
        \Magento\Framework\DataObjectFactory $objectFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperLayout = $helperLayout;
        $this->objectFactory = $objectFactory;
    }

    /**
     * @param $type
     * @param bool $isAmp
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setType($type, $isAmp = false)
    {
        $blocks = $this->helperLayout->getBlocks($type);

        if ($blocks && is_array($blocks) && !empty($blocks)) {
            foreach ($blocks as $data) {
                $object = $this->objectFactory->create(['data' => $data]);
                if ($object->getFrontendBlock()) {
                    $alias = $object->getValue();
                    $name = 'am.blog.' . $type . '.' . $alias;
                    $block = $this->getLayout()->createBlock($object->getFrontendBlock(), $name);
                    if ($isAmp) {
                        $block->setAmpTemplate();
                    }

                    if ($block) {
                        $this->append($block, $alias);
                    }
                }
            }
        }

        return $this;
    }
}
