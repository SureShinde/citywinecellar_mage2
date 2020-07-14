<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Model\Staff;

/**
 * class \Magestore\Appadmin\Model\Staff\Role
 *
 * Web POS Authorization Role model
 * Use to work with Web POS role table
 * Methods:
 *  getValuesForForm
 *
 * @category    Magestore
 * @package     Magestore_Appadmin
 * @module      Appadmin
 * @author      Magestore Developer
 */
class Role extends \Magento\Framework\Model\AbstractModel implements \Magestore\Appadmin\Api\Data\Staff\RoleInterface
{
    /**
     * @var \Magestore\Appadmin\Api\Event\DispatchServiceInterface
     */
    protected $dispatchService;
    /**
     * @var \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
     */
    protected $staffRepository;

    /**
     * Role constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Appadmin\Model\ResourceModel\Staff\Role $resource
     * @param \Magestore\Appadmin\Model\ResourceModel\Staff\Role\Collection $resourceCollection
     * @param \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService
     * @param \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Appadmin\Model\ResourceModel\Staff\Role $resource,
        \Magestore\Appadmin\Model\ResourceModel\Staff\Role\Collection $resourceCollection,
        \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService,
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->dispatchService = $dispatchService;
        $this->staffRepository = $staffRepository;
    }

    public function beforeDelete()
    {
        $staffs = $this->staffRepository->getByRoleId($this->getRoleId());
        foreach ($staffs as $staff) {
            $this->dispatchService->dispatchEventForceSignOut($staff->getStaffId());
        }

        return parent::beforeDelete();
    }

    /**
     * get location list for form select element
     * return array
     */
    public function getValuesForForm(){
        $collection = $this->getCollection();
        $options = array();
        if($collection->getSize() > 0){
            foreach ($collection as $role){
                $options[] = array('value' => $role->getId(), 'label' => $role->getData('display_name'));
            }
        }
        return $options;
    }

    /**
     * @return array
     */
    public function getHashOption() {
        $options = array();
        $collection = $this->getCollection();
        foreach ($collection as $role) {
            $options[$role->getId()] = $role['display_name'];
        }
        return $options;
    }

    /**
    /**
     * Get role id
     *
     * @api
     * @return string|null
     */
    public function getRoleId()
    {
        return $this->getData(self::ROLE_ID);
    }

    /**
     * Set role id
     *
     * @api
     * @param string $roleId
     * @return $this
     */
    public function setRoleId($roleId)
    {
        $this->setData(self::ROLE_ID, $roleId);
        return $this;
    }

    /**
     * Get name
     *
     * @api
     * @return string|null
     */
    public function getName() {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     *
     * @api
     * @param string $name
     * @return $this
     */
    public function setName($name) {
        $this->setData(self::NAME, $name);
        return $this;
    }
    /**
     * Get description
     *
     * @api
     * @return string|null
     */
    public function getDescription() {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set description
     *
     * @api
     * @param string $description
     * @return $this
     */
    public function setDescription($description) {
        $this->setData(self::DESCRIPTION, $description);
        return $this;
    }

    /**
     * Get description
     *
     * @api
     * @return string|null
     */
    public function getMaximumDiscountPercent() {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set discount
     *
     * @api
     * @param string $discount
     * @return $this
     */
    public function setMaximumDiscountPercent($discount) {
        $this->setData(self::DESCRIPTION, $discount);
        return $this;
    }
}