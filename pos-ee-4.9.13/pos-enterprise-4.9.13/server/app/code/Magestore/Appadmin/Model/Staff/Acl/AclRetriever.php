<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Model\Staff\Acl;

class AclRetriever
{
    protected $_roleCollectionFactory;
    protected $roleFactory;
    protected $_ruleCollectionFactory;
    protected $_logger;
    protected $_aclBuilder;

    public function __construct(
        \Magestore\Appadmin\Model\Staff\Acl\Builder $aclBuilder,
        \Magestore\Appadmin\Model\ResourceModel\Staff\Role\CollectionFactory $roleCollectionFactory,
        \Magestore\Appadmin\Model\Staff\RoleFactory $roleFactory,
        \Magestore\Appadmin\Model\ResourceModel\Staff\AuthorizationRule\CollectionFactory $authorizationRuleCollectionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_logger = $logger;
        $this->_ruleCollectionFactory = $authorizationRuleCollectionFactory;
        $this->_roleCollectionFactory = $roleCollectionFactory;
        $this->roleFactory = $roleFactory;
        $this->_aclBuilder = $aclBuilder;
    }

    public function getAllowedResourcesByRole($roleId)
    {
        $allowedResources = [];
        $rulesCollection = $this->_ruleCollectionFactory->create();
        /* @var \Magestore\Appadmin\Model\ResourceModel\Staff\AuthorizationRule\Collection $rulesCollection*/
        $rulesCollection->getByRoles($roleId)->load();
        /** @var \Magestore\Appadmin\Model\Staff\AuthorizationRule $ruleItem */
        foreach ($rulesCollection->getItems() as $ruleItem) {
            $resourceId = $ruleItem->getResourceId();
            $allowedResources[] = $resourceId;
        }

        return $allowedResources;
    }
    public function getMaxDiscountPercentByRole($roleId)
    {
        $role = $this->roleFactory->create()->load((int)$roleId);
        return $role->getMaxDiscountPercent();
    }
}
