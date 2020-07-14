<?php
namespace Magestore\Webpos\Plugin\Appadmin\Model\Staff\Acl\AclResource;

class Provider
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /*
     *
     */
    protected $webposManagement;

    /**
     * Provider constructor.
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
    )
    {
        $this->moduleManager = $moduleManager;
        $this->webposManagement = $webposManagement;
    }

    /**
     * @param \Magestore\Appadmin\Model\Staff\Acl\AclResource\Provider $aclResourceProvider
     * @param $aclResource
     * @return []
     */
    public function afterGetAclResources(
        $aclResourceProvider,
        $aclResource
    )
    {
        /* if not MSI and is Standard mode => remove this permission */
        if(!$this->webposManagement->isMSIEnable() && $this->webposManagement->isWebposStandard()){
            return $this->removeCheckExternalStock($aclResource);
        }
        return $aclResource;
    }

    /**
     * @param $acl
     * @return mixed
     */
    public function removeCheckExternalStock(&$acl)
    {
        if (is_array($acl) && count($acl)) {
            for ($index = 0; $index < count($acl); $index++) {
                if (isset($acl[$index]["id"]) && $acl[$index]["id"] === 'Magestore_Webpos::check_external_stock') {
                    unset($acl[$index]);
                }
                if (isset($acl[$index]['children']) && is_array($acl[$index]['children']) && count($acl[$index]['children'])) {
                    $this->removeCheckExternalStock($acl[$index]['children']);
                }
            }
        }
        return $acl;
    }
}