<?php

namespace Magestore\Webpos\Observer\Pos;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class ForceChangePos implements ObserverInterface {
    /**
     * @var \Magestore\Webpos\Api\Pos\PosRepositoryInterface
     */
    protected $posRepository;
    /**
     * @var \Magestore\Webpos\Api\Staff\SessionRepositoryInterface
     */
    protected $sessionRepository;

    /**
     * ForceSignOut constructor.
     * @param \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository
     */
    public function __construct(
        \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository,
        \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository
    ) {
        $this->posRepository = $posRepository;
        $this->sessionRepository = $sessionRepository;
    }

    /**
     * @param EventObserver $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        $staffId = $observer->getStaffId();
        $posId = $observer->getPosId();

        $sessions = $this->sessionRepository->getListByStaffId($staffId);
        $sessions->addFieldToFilter('pos_id', $posId);

        foreach ($sessions as $session) {
            if($session->getHasException() != \Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_FORCE_SIGN_OUT
                && $session->getHasException() != \Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_FORCE_CHANGE_POS) {
                $session->setHasException(\Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_FORCE_CHANGE_POS);
                $session->setLocationId(null);
                $session->setPosId(null);
                $this->sessionRepository->save($session);
            }
        }

        $pos = $this->posRepository->getById($posId);
        $pos->setStaffId(null);
        $this->posRepository->save($pos);

        return $this;
    }
}