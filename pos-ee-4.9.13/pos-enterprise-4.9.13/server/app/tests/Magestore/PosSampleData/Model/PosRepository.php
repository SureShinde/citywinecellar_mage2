<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Model;
/**
 * Class Currency
 * @package Magestore\Webpos\Model\Config\Data
 */
class PosRepository implements \Magestore\PosSampleData\Api\PosRepositoryInterface
{
    /**
     * @var StaffFactory
     */
    protected $sessionFactory;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Staff\Session
     */
    protected $sessionResource;
    /**
     * @var \Magestore\Webpos\Api\Data\Staff\SessionSearchResultsInterface
     */
    protected $sessionSearchResults;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Staff\Session\CollectionFactory
     */
    protected $sessionCollectionFactory;
    /**
     * @var \Magestore\Appadmin\Model\ResourceModel\Staff\AuthorizationRule\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
     */
    protected $staffRepository;

    /**
     * @var \Magestore\Webpos\Model\Pos\PosFactory
     */
    protected $posFactory;

    /**
     * @var \Magestore\Appadmin\Model\Staff\StaffFactory
     */
    protected $staffFactory;

    /**
     * @var \Magestore\Webpos\Model\Pos\PosRepository
     */
    protected $posRepository;
    /**
     * @var \Magestore\Webpos\Api\Staff\SessionRepositoryInterface
     */
    protected $sessionRepository;
    /**
     * @var \Magestore\Webpos\Api\Event\DispatchServiceInterface
     */
    protected $dispatchService;
    /**
     * StaffRepository constructor.
     * @param StaffFactory $sessionFactory
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Staff\SessionInterfaceFactory $sessionFactory,
        \Magestore\Webpos\Model\ResourceModel\Staff\Session $sessionResource,
        \Magestore\Webpos\Model\ResourceModel\Staff\Session\CollectionFactory $sessionCollectionFactory,
        \Magestore\Webpos\Api\Data\Staff\SessionSearchResultsInterfaceFactory $sessionSearchResultsInterfaceFactory,
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository,
        \Magestore\Appadmin\Model\ResourceModel\Staff\AuthorizationRule\CollectionFactory $collectionFactory,
        \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository,
        \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository,
        \Magestore\Webpos\Model\Pos\PosFactory $posFactory,
        \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService,
        \Magestore\Appadmin\Model\Staff\StaffFactory $staffFactory

    ){
        $this->sessionFactory = $sessionFactory;
        $this->sessionResource = $sessionResource;
        $this->sessionSearchResults = $sessionSearchResultsInterfaceFactory;
        $this->sessionCollectionFactory = $sessionCollectionFactory;
        $this->collectionFactory = $collectionFactory;
        $this->staffRepository = $staffRepository;
        $this->posFactory = $posFactory;
        $this->staffFactory = $staffFactory;
        $this->posRepository = $posRepository;
        $this->sessionRepository = $sessionRepository;
        $this->dispatchService = $dispatchService;
    }
    /**
     * Save Role.
     *
     * @param \Magestore\PosSampleData\Api\Data\PosInterface $pos
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function forceSignOut(\Magestore\PosSampleData\Api\Data\PosInterface $pos) {
        $posModel = $this->posFactory->create()->load($pos->getPosName(), 'pos_name');
        $this->signOut($posModel);
        return true;
    }

    /**
     * @param $posModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function signOut($posModel) {
        if ($posModel->getId()){
            // dispatch event to logout POS
            $this->dispatchService->dispatchEventForceSignOut($posModel->getStaffId(), $posModel->getPosId());
            $posModel->setStaffId(null);
            $this->posRepository->save($posModel);
            $this->sessionRepository->signOutPos($posModel->getId());
        }
    }
}