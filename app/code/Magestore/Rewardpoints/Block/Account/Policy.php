<?php
namespace Magestore\Rewardpoints\Block\Account;

class Policy extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_modelPageFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProviderFactory
     */
    protected $_filterProviderFactory;

    /**
     * Policy constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Cms\Model\PageFactory $modelPageFactory
     * @param \Magento\Cms\Model\Template\FilterProviderFactory $filterProviderFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\PageFactory $modelPageFactory,
        \Magento\Cms\Model\Template\FilterProviderFactory $filterProviderFactory

    )
    {
        parent::__construct($context, []);
        $this->_modelPageFactory = $modelPageFactory;
        $this->_filterProviderFactory = $filterProviderFactory;
    }

    /**
     * @return mixed
     */

    public function getPageIdentifier(){
        return $this->_scopeConfig->getValue(
            \Magestore\Rewardpoints\Helper\Policy::XML_PATH_POLICY_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * @return mixed
     */
    public function getPageId(){
        $pageId = $this->_modelPageFactory->create()->checkIdentifier($this->getPageIdentifier(), $this->_storeManager->getStore()->getId());
        return $pageId;
    }

    /**
     * @return \Magento\Cms\Model\Page
     */
    public function getPage(){
        return $this->_modelPageFactory->create()->load($this->getPageId());
    }

    /**
     * @return string
     */
    protected function _toHtml(){

        $html = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
        $html .= $this->_filterProviderFactory->create()->getPageFilter()->filter($this->getPage()->getContent());
        return $html;
    }
}
