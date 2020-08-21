<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Pos\Pos\Edit\Buttons;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;

/**
 * Button Force sign out
 */
class ForceSignOut extends Generic
{
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface
     */
    protected $shiftRepository;

    /**
     * @var \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
     */
    protected $staffRepository;

    /**
     * ForceSignOut constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository
     * @param \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magestore\Webpos\Helper\Data $helper,
        \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository,
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository
    ) {
        parent::__construct($context, $registry, $authorization);
        $this->helper = $helper;
        $this->shiftRepository = $shiftRepository;
        $this->staffRepository = $staffRepository;
    }
    
    /**
     * Get Button Data
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getButtonData()
    {
        if (!$this->getPos() || !$this->getPos()->getId()) {
            return [];
        }

        if ($this->getPos() && !$this->getPos()->getStaffId()) {
            return [];
        }

        if (!$this->authorization->isAllowed('Magestore_Webpos::pos')) {
            return [];
        }

        $url = $this->getUrl('*/*/forceSignOut', ['id' => $this->getPos()->getId()]);
        $onClick = sprintf("location.href = '%s';", $url);
        
        /* notify to admin if there is an openning session on pos */
        if ($this->getPos()->getStaffId() && $this->helper->isEnableSession()) {
            $currentShift = $this->shiftRepository->getCurrentShiftByPosId($this->getPos()->getPosId());
            $staff = $this->staffRepository->getById($this->getPos()->getStaffId());
            if ($staff->getName() && $currentShift->getId()) {
                $onClick = sprintf(
                    "deleteConfirm(
                        '%s', 
                        '%s'
                    )",
                    __("POS is still working in the session. Are you sure to force sign-out?"),
                    $url
                );
            }
        }

        return [
            'label' => __('Force Sign-out'),
            'class' => 'force_sign_out',
            'on_click' => $onClick,
            'sort_order' => 16
        ];
    }
}
