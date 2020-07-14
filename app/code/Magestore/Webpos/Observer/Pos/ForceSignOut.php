<?php

namespace Magestore\Webpos\Observer\Pos;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class ForceSignOut implements ObserverInterface {
    /**
     * @var \Magestore\Webpos\Api\Staff\SessionRepositoryInterface
     */
    protected $sessionRepository;

    /**
     * ForceSignOut constructor.
     * @param \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository
     */
    public function __construct(
        \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository
    ) {
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
        if($posId) {
            $sessions->addFieldToFilter('pos_id', $posId);
        }

        foreach ($sessions as $session) {
            if($session->getHasException() != \Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_FORCE_SIGN_OUT) {
                $session->setHasException(\Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_FORCE_SIGN_OUT);
                $this->sessionRepository->save($session);
            }
        }

        return $this;
    }
}