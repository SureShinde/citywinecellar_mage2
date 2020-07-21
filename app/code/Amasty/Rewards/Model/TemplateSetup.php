<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model;

use Magento\Email\Model\ResourceModel\Template;
use Magento\Email\Model\TemplateFactory;

class TemplateSetup
{
    /**
     * @var Template
     */
    private $templateResource;

    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    public function __construct(
        Template $templateResource,
        TemplateFactory $templateFactory
    ) {
        $this->templateResource = $templateResource;
        $this->templateFactory = $templateFactory;
    }

    /**
     * @param string $templateCode
     * @param string $templateLabel
     *
     * @return \Magento\Email\Model\Template
     */
    public function createTemplate($templateCode, $templateLabel)
    {
        /** @var \Magento\Email\Model\Template $template */
        $template = $this->templateFactory->create();

        $template->setForcedArea($templateCode);
        $template->loadDefault($templateCode);
        $template->setData('orig_template_code', $templateCode);
        $template->setData('template_variables', \Zend_Json::encode($template->getVariablesOptionArray(true)));
        $template->setData('template_code', $templateLabel);
        $template->setTemplateType(\Magento\Email\Model\Template::TYPE_HTML);
        $template->setId(null);

        if (!$this->templateResource->checkCodeUsage($template)) {
            $this->templateResource->save($template);
        }

        return $template;
    }
}
