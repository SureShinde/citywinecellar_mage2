<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Model\ResourceModel\Staff;

use Magento\Framework\Exception\LocalizedException;
/**
 * Class Role
 * @package Magestore\Appadmin\Model\ResourceModel\Staff
 */
class Role extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('webpos_authorization_role', 'role_id');
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->checkRoleNameExist($object);
        return parent::_beforeSave($object);
    }

    /**
     * Check the role name before saving
     *
     * @param \Magestore\Appadmin\Model\Staff\Role $object
     * @return $this
     */
    public function checkRoleNameExist(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            ['count' => 'COUNT(role_id)']
        )->where('name =?', $object->getName())->where('role_id <>?', $object->getRoleId());

        $results = $this->getConnection()->fetchAll($select);
        foreach ($results as $result) {
            if ($result['count']) {
                throw new LocalizedException(
                    __('Role Name already exists.')
                );
            }
        }
        return $this;
    }
}