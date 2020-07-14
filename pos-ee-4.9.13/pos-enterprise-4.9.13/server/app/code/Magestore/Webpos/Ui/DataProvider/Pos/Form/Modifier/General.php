<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\DataProvider\Pos\Form\Modifier;

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form;

class General extends AbstractModifier
{
    /**
     * @var \Magento\Store\Ui\Component\Listing\Column\Store\Options
     */
    protected $optionStoreView;

    /**
     * @var string
     */
    protected $groupContainer = 'general_information';

    /**
     * @var string
     */
    protected $groupLabel = 'General Information';

    /**
     * @var int
     */
    protected $sortOrder = 80;

    /**
     * @var bool
     */
    protected $opened = true;
    /**
     * @var \Magestore\Webpos\Model\Source\Adminhtml\CustomerGroup
     */
    protected $customerGroup;
    /**
     * @var \Magestore\Webpos\Model\Source\Adminhtml\Status
     */
    protected $status;
    /**
     * @var \Magestore\Webpos\Model\Source\Adminhtml\Location
     */
    protected $location;
    /**
     * @var \Magestore\Webpos\Model\Source\Adminhtml\Pos
     */
    protected $pos;

    /**
     * @var \Magestore\Appadmin\Model\Source\Adminhtml\Role
     */
    protected $role;

    /**
     * @var \Magestore\Webpos\Model\Source\Adminhtml\Staff
     */
    protected $staff;
    /**
     * @var \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
     */
    protected $staffRepository;


    /**
     * General constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param \Magento\Store\Ui\Component\Listing\Column\Store\Options $optionStoreView
     * @param \Magestore\Webpos\Model\Source\Adminhtml\CustomerGroup $customerGroup
     * @param \Magestore\Webpos\Model\Source\Adminhtml\Status $status
     * @param \Magestore\Webpos\Model\Source\Adminhtml\Location $location
     * @param \Magestore\Webpos\Model\Source\Adminhtml\Pos $pos
     * @param \Magestore\Appadmin\Model\Source\Adminhtml\Role $role
     * @param \Magestore\Webpos\Model\Source\Adminhtml\Staff $staff
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Ui\Component\Listing\Column\Store\Options $optionStoreView,
        \Magestore\Webpos\Model\Source\Adminhtml\CustomerGroup $customerGroup,
        \Magestore\Webpos\Model\Source\Adminhtml\Status $status,
        \Magestore\Webpos\Model\Source\Adminhtml\Location $location,
        \Magestore\Webpos\Model\Source\Adminhtml\Pos $pos,
        \Magestore\Appadmin\Model\Source\Adminhtml\Role $role,
        \Magestore\Webpos\Model\Source\Adminhtml\Staff $staff,
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository
    )
    {
        parent::__construct($objectManager, $registry, $request, $urlBuilder);
        $this->optionStoreView = $optionStoreView;
        $this->customerGroup = $customerGroup;
        $this->status = $status;
        $this->location = $location;
        $this->pos = $pos;
        $this->role = $role;
        $this->staff = $staff;
        $this->staffRepository = $staffRepository;
    }

    /**
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function modifyData(array $data)
    {
        $posId = $this->request->getParam('id');
        if (isset($data[$posId]['staff_id']) && $data[$posId]['staff_id']) {
            $staffId = $data[$posId]['staff_id'];

            try {
                $staffModel = $this->staffRepository->getById($staffId);
                if ($staffModel->getStaffId()) {
                    $data[$posId]['staff_name'] = $staffModel->getName();
                }
            } catch (\Exception $e) {
                $data[$posId]['staff_name'] = __('N/A');
            }

        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                'pos_information' => [
                    'children' => $this->getGeneralChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('POS'),
                                'collapsible' => true,
                                'dataScope' => 'data',
                                'visible' => $this->getVisible(),
                                'opened' => $this->getOpened(),
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => 1
                            ],
                        ],
                    ],
                ],

            ]
        );
        return $meta;
    }

    public function getGeneralChildren()
    {
        if ($this->request->getParam('default_location')) {
            $defaultLocation = $this->request->getParam('default_location');
        } else {
            $defaultLocation = 0;
        }

        $pos = $this->registry->registry('current_pos');

        $staffId = null;
        if ($pos && $pos->getId()) {
            $staffId = $pos->getStaffId();
        }

        $children = [
            'pos_name' => $this->addFormFieldText(__('POS Name'), 'input', 10, ['required-entry' => true]),
            'location_id' => $this->addFormFieldSelectText(
                'Magestore_Webpos/js/form/element/location-form',
                __('Location'),
                $this->location->toOptionArray(),
                20,
                ['required-entry' => true],
                $defaultLocation,
                '',
                null,
                $staffId ? true : false
            ),
            'status' => $this->addFormFieldSelect('Status', $this->status->toOptionArray(), 30, ['required-entry' => false]),
        ];
        if ($this->request->getParam('id')) {
            $children['staff_name'] = $this->addFormFieldLabel(__('Current Staff'), 'input', 25, ['required-entry' => false], '', 'N/A');
        }
        return $children;
    }


}