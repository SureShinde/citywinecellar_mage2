<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Controller\Adminhtml\Banners;

use Magento\Framework\Controller\ResultFactory;

class Options extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository $_productAttributeRepository
     */
    protected $_productAttributeRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository
    ) {
        parent::__construct($context);
        $this->_productAttributeRepository = $productAttributeRepository;
    }

    public function execute()
    {
        $result = '<input id="attr_value" name="attr_value[]" value="" class="input-text" type="text" />';

        $code = $this->getRequest()->getParam('code');
        if (!$code) {
            $this->getResponse()->setBody($result);
            return;
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $attribute = $this->_productAttributeRepository->get($code);
        if (!$attribute) {
            $this->getResponse()->setBody($result);
            return;
        }

        if (!in_array($attribute->getFrontendInput(), ['select', 'multiselect'])) {
            $this->getResponse()->setBody($result);
            return;
        }

        $options = $attribute->getFrontend()->getSelectOptions();

        $result = '<select id="attr_value" name="attr_value[]" class="select">';
        foreach ($options as $option) {
            $result .= '<option value="' . $option['value'] . '">' . $option['label'] . '</option>';
        }

        $result .= '</select>';

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setContents($result);

        return $resultRaw;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_PromoBanners::ampromobanners');
    }
}
