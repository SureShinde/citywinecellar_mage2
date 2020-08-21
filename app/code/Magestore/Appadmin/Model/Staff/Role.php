<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Model\Staff;

/**
 * Model staff Role
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
     *
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

    /**
     * Before Delete
     *
     * @return Role
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {
        $staffs = $this->staffRepository->getByRoleId($this->getRoleId());
        foreach ($staffs as $staff) {
            $this->dispatchService->dispatchEventForceSignOut($staff->getStaffId());
        }

        return parent::beforeDelete();
    }

    /**
     * Get location list for form select element
     *
     * @return array
     */
    public function getValuesForForm()
    {
        $collection = $this->getCollection();
        $options = [];
        if ($collection->getSize() > 0) {
            foreach ($collection as $role) {
                $options[] = ['value' => $role->getId(), 'label' => $role->getData('display_name')];
            }
        }
        return $options;
    }

    /**
     * Get Hash Option
     *
     * @return array
     */
    public function getHashOption()
    {
        $options = [];
        $collection = $this->getCollection();
        foreach ($collection as $role) {
            $options[$role->getId()] = $role['display_name'];
        }
        return $options;
    }

    /**
     * Get role id
     *
     * @return string|null
     * @api
     */
    public function getRoleId()
    {
        return $this->getData(self::ROLE_ID);
    }

    /**
     * Set role id
     *
     * @param string $roleId
     * @return $this
     * @api
     */
    public function setRoleId($roleId)
    {
        $this->setData(self::ROLE_ID, $roleId);
        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     * @api
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     * @api
     */
    public function setName($name)
    {
        $this->setData(self::NAME, $name);
        return $this;
    }

    /**
     * Get description
     *
     * @return string|null
     * @api
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     * @api
     */
    public function setDescription($description)
    {
        $this->setData(self::DESCRIPTION, $description);
        return $this;
    }

    /**
     * Get description
     *
     * @return string|null
     * @api
     */
    public function getMaximumDiscountPercent()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set discount
     *
     * @param string $discount
     * @return $this
     * @api
     */
    public function setMaximumDiscountPercent($discount)
    {
        $this->setData(self::DESCRIPTION, $discount);
        return $this;
    }
}
