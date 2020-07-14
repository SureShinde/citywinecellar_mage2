<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Ui\DataProvider\Staff\Form\Modifier;

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
     * @var \Magestore\Appadmin\Model\Source\Adminhtml\CustomerGroup
     */
    protected $customerGroup;
    /**
     * @var \Magestore\Appadmin\Model\Source\Adminhtml\Status
     */
    protected $status;
    /**
     * @var \Magestore\Webpos\Model\Source\Adminhtml\Location
     */
    protected $location;
    /**
     * @var \Magestore\Appadmin\Model\Source\Adminhtml\Pos
     */
    protected $pos;

    /**
     * @var \Magestore\Appadmin\Model\Source\Adminhtml\Role $role
     */
    protected $role;

    /**
     * General constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param \Magento\Store\Ui\Component\Listing\Column\Store\Options $optionStoreView
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Ui\Component\Listing\Column\Store\Options $optionStoreView,
        \Magestore\Webpos\Model\Source\Adminhtml\CustomerGroup $customerGroup,
        \Magestore\Appadmin\Model\Source\Adminhtml\Status $status,
        \Magestore\Webpos\Model\Source\Adminhtml\Location $location,
        \Magestore\Webpos\Model\Source\Adminhtml\Pos $pos,
        \Magestore\Appadmin\Model\Source\Adminhtml\Role $role
    )
    {
        parent::__construct($objectManager, $registry, $request, $urlBuilder);
        $this->optionStoreView = $optionStoreView;
        $this->customerGroup = $customerGroup;
        $this->status = $status;
        $this->location = $location;
        $this->pos = $pos;
        $this->role = $role;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
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
                'staff_information' => [
                    'children' => $this->getGeneralChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Staff Information'),
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
                'user_settings' => [
                    'children' => $this->getUserSettings(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('POS permission'),
                                'collapsible' => true,
                                'dataScope' => 'data',
                                'visible' => $this->getVisible(),
                                'opened' => $this->getOpened(),
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => 2
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
        $children = [
            'username' => $this->addFormFieldText('User Name', 'input', 10, ['required-entry' => true]),
            'password' => $this->addFormFieldPassword(
                __('Password'),
                'input',
                20,
                [
                    'validate-admin-password' => true
                ],
                'input-text required-entry validate-admin-password'
            ),
            'password_confirmation' => $this->addFormFieldPassword(
                __('Password Confirmation'),
                'input',
                30,
                [
                    'equalTo' => '#password'
                ],
                'input-text required-entry validate-cpassword'
            ),
            'name' => $this->addFormFieldText('Display Name', 'input', 40, ['required-entry' => true]),
            'email' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'label' => __('Email Address'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'sortOrder' => 50,
                            'validation' => [
                                'required-entry' => true,
                                'validate-email' => true
                            ],
                        ],
                    ],
                ],
            ],
            'status' => $this->addFormFieldSelect('Status', $this->status->toOptionArray(), 70, ['required-entry' => true]),
        ];

        if (!$this->request->getParam('id')) {
            $children['password']['arguments']['data']['config']['validation']['required-entry'] = true;
            $children['password_confirmation']['arguments']['data']['config']['validation']['required-entry'] = true;
        }else{
            $children['password']['arguments']['data']['config']['label'] = __("New Password");
        }
        return $children;
    }


    public function getUserSettings()
    {
        $children = [
            'role_id' => $this->addFormFieldSelect('Role', $this->role->toOptionArray(), 10, ['required-entry' => true]),
            'location_ids' => $this->addFormFieldMultiSelect('Location', $this->location->toOptionArray(), 20, ['required-entry' => true])
        ];
        return $children;
    }


    /**
     * Retrieve countries
     *
     * @return array|null
     */
    public function getStoreViews()
    {
        return $this->optionStoreView->toOptionArray();
    }
}