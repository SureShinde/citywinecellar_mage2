<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Block\Adminhtml\Staff\Role\Edit\Tab;

use Magento\User\Controller\Adminhtml\User\Role\SaveRole;

/**
 * Rolesedit Tab Display Block.
 *
 * @api
 * @since 100.0.2
 */
class Permission extends \Magento\Backend\Block\Widget\Form implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'Magestore_Appadmin::role/edit.phtml';

    /**
     * Root ACL Resource
     *
     * @var \Magento\Framework\Acl\RootResource
     */
    protected $_rootResource;

    /**
     * Rules collection factory
     *
     * @var \Magento\Authorization\Model\ResourceModel\Rules\CollectionFactory
     */
    protected $_rulesCollectionFactory;

    /**
     * Acl builder
     *
     * @var \Magento\Authorization\Model\Acl\AclRetriever
     */
    protected $_aclRetriever;

    /**
     * @var \Magestore\Appadmin\Model\Staff\Acl\AclResource\ProviderInterface
     */
    protected $_aclResourceProvider;

    /**
     * @var \Magento\Integration\Helper\Data
     */
    protected $_integrationData;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     * @since 100.1.0
     */
    protected $coreRegistry = null;

    /**
     * @var \Magestore\Appadmin\Model\Staff\Acl\AclRetriever
     */
    protected $_webposAclRetriever;

    /**
     * @var \Magento\Framework\Json\Encoder
     */
    protected $encoder;

    /**
     * @var \Magestore\Appadmin\Model\Staff\Acl\LabelResources
     */
    protected $labelResources;


    /**
     *
     */
    protected function _construct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_aclResourceProvider = $objectManager->get('\Magestore\Appadmin\Model\Staff\Acl\AclResource\ProviderInterface');
        $this->_rootResource = $objectManager->get('\Magestore\Appadmin\Model\Staff\Acl\RootResource');
        $this->_webposAclRetriever = $objectManager->get('\Magestore\Appadmin\Model\Staff\Acl\AclRetriever');
        $this->encoder = $objectManager->get('Magento\Framework\Json\Encoder');

        $rid = $this->_request->getParam('id', false);
        $this->setSelectedResources($this->_webposAclRetriever->getAllowedResourcesByRole($rid));
    }


    /**
     * Permission constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever
     * @param \Magento\Framework\Acl\RootResource $rootResource
     * @param \Magento\Authorization\Model\ResourceModel\Rules\CollectionFactory $rulesCollectionFactory
     * @param \Magestore\Appadmin\Model\Staff\Acl\AclResource\ProviderInterface $aclResourceProvider
     * @param \Magento\Integration\Helper\Data $integrationData
     * @param \Magestore\Appadmin\Model\Staff\Acl\LabelResources $labelResources
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        \Magento\Framework\Acl\RootResource $rootResource,
        \Magento\Authorization\Model\ResourceModel\Rules\CollectionFactory $rulesCollectionFactory,
        \Magestore\Appadmin\Model\Staff\Acl\AclResource\ProviderInterface $aclResourceProvider,
        \Magento\Integration\Helper\Data $integrationData,
        \Magestore\Appadmin\Model\Staff\Acl\LabelResources $labelResources,
        array $data = []
    ) {
        $this->_aclRetriever = $aclRetriever;
        $this->_rootResource = $rootResource;
        $this->_rulesCollectionFactory = $rulesCollectionFactory;
        $this->_aclResourceProvider = $aclResourceProvider;
        $this->_integrationData = $integrationData;
        $this->labelResources = $labelResources;
        parent::__construct($context, $data);
    }

    /**
     * Set core registry
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @return void
     * @deprecated 100.1.0
     * @since 100.1.0
     */
    public function setCoreRegistry(\Magento\Framework\Registry $coreRegistry)
    {
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Get core registry
     *
     * @return \Magento\Framework\Registry
     * @deprecated 100.1.0
     * @since 100.1.0
     */
    public function getCoreRegistry()
    {
        if (!($this->coreRegistry instanceof \Magento\Framework\Registry)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Registry::class);
        } else {
            return $this->coreRegistry;
        }
    }

    /**
     * Get tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Role Resources');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Whether tab is visible
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check if everything is allowed
     *
     * @return bool
     */
    public function isEverythingAllowed()
    {
        $selectedResources = $this->getSelectedResources();
        $id = $this->_rootResource->getId();
        return in_array($id, $selectedResources);
    }

    /**
     * Get selected resources
     *
     * @return array|mixed|\string[]
     * @since 100.1.0
     */
    public function getSelectedResources()
    {
        $selectedResources = $this->getData('selected_resources');
        if (empty($selectedResources)) {
            $allResource = $this->getCoreRegistry()->registry(SaveRole::RESOURCE_ALL_FORM_DATA_SESSION_KEY);
            if ($allResource) {
                $selectedResources = [$this->_rootResource->getId()];
            } else {
                $selectedResources = $this->getCoreRegistry()->registry(SaveRole::RESOURCE_FORM_DATA_SESSION_KEY);
            }

            if (null === $selectedResources) {
                $rid = $this->_request->getParam('rid', false);
                $selectedResources = $this->_aclRetriever->getAllowedResourcesByRole($rid);
            }

            $this->setData('selected_resources', $selectedResources);
        }
        return $selectedResources;
    }

    /**
     * Get Json Representation of Resource Tree
     *
     * @return array
     */
    public function getTree()
    {
        return $this->_integrationData->mapResources($this->getAclResources());
    }

    /**
     * Get requested permissions tree.
     *
     * @return string
     */
    public function getResourcesTreeJson()
    {
        $aclResourcesTree = $this->getTree();

        return $this->encoder->encode($aclResourcesTree);
    }

    /**
     * Return an array of selected resource ids.
     *
     * If everything is allowed then iterate through all
     * available resources to generate a comprehensive array of all resource ids, rather than just
     * returning "Magento_Backend::all".
     *
     * @return string
     */
    public function getSelectedResourcesJson()
    {
        $selectedResources = $this->getSelectedResources();
        return $this->encoder->encode($selectedResources);
    }


    /**
     * Get lit of all ACL resources declared in the system.
     *
     * @return array
     */
    private function getAclResources()
    {
        $resources = $this->_aclResourceProvider->getAclResources();
        $configResource = array_filter(
            $resources,
            function ($node) {
                return $node['id'] == 'Magestore_Webpos::webpos';
            }
        );
        $configResource = reset($configResource);
        return isset($configResource['children']) ? $configResource['children'] : [];
    }

    /**
     * Get label resource
     *
     * @return string
     */
    public function getLabelResources()
    {
        return $this->encoder->encode(array_values($this->labelResources->getLabelResources()));
    }
}

