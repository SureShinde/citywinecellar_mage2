<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\DataProvider\Location\Form\Modifier;

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form;

class General extends AbstractModifier {
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
     * @var string
     */
    protected $addressContainer = 'address_information';

    /**
     * @var string
     */
    protected $addressLabel = 'Address';

    /**
     * @var int
     */
    protected $sortOrder = 80;

    /**
     * @var bool
     */
    protected $opened = true;

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
        \Magento\Store\Ui\Component\Listing\Column\Store\Options $optionStoreView
    ) {
        parent::__construct($objectManager, $registry, $request, $urlBuilder);
        $this->optionStoreView = $optionStoreView;
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
                $this->groupContainer => [
                    'children' => $this->getGeneralChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->groupLabel),
                                'collapsible' => true,
                                'dataScope' => 'data',
                                'visible' => $this->getVisible(),
                                'opened' => $this->getOpened(),
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => $this->getSortOrder()
                            ],
                        ],
                    ],
                ],
                $this->addressContainer => [
                    'children' => $this->getAddressChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->addressLabel),
                                'collapsible' => true,
                                'dataScope' => 'data',
                                'visible' => $this->getVisible(),
                                'opened' => $this->getOpened(),
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => '20'
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
            'name' => $this->addFormFieldText('Location Name', 'input', 20, ['required-entry' => true]),
            'email' => $this->addFormFieldText('Contact Email', 'input', 30),
            'telephone' => $this->addFormFieldText('Telephone', 'input', 40),
            'description' => $this->addFormFieldTextArea('Description', 50)
        ];

        return $children;
    }

    public function getAddressChildren()
    {
        $children = [
            'street' => $this->addFormFieldText('Street', 'input', 20, ['required-entry' => true]),
            'city' => $this->addFormFieldText('City', 'input', 30, ['required-entry' => true]),
            'region' => $this->addFormFieldText('State or Province', 'input', 40, ['required-entry' => true]),
            'country_id' => $this->addFormFieldTextArea('Country', 50, ['required-entry' => true]),
            'postcode' => $this->addFormFieldTextArea('Zip/Postal Code', 60, ['required-entry' => true])
//            'store_id' => $this->addFormFieldSelect('Store View', $this->getStoreViews(), 50, ['required-entry' => true])
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