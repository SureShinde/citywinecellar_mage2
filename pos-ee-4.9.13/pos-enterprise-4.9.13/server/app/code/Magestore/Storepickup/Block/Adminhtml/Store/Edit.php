<?php

/**
 * Magestore.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storepickup
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storepickup\Block\Adminhtml\Store;

/**
 * Store Edit Form Container.
 *
 * @category Magestore
 * @package  Magestore_Storepickup
 * @module   Storepickup
 * @author   Magestore Developer
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\Storepickup\Helper\Data
     */
    protected $storePickUpHelper;

    /**
     * Edit constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magestore\Storepickup\Helper\Data $storePickUpHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magestore\Storepickup\Helper\Data $storePickUpHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    )
    {
        $this->storePickUpHelper = $storePickUpHelper;
        $this->objectManager = $objectManager;
        $this->_directoryHelper = $directoryHelper;
        parent::__construct($context, $data);
    }

    /**
     */
    protected function _construct()
    {
        $this->_objectId = 'storepickup_id';
        $this->_blockGroup = 'Magestore_Storepickup';
        $this->_controller = 'adminhtml_store';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Store'));
        $this->buttonList->update('save', 'data_attribute', [
            'mage-init' => ['button' => ['event' => 'saveOnly', 'target' => '#edit_form']],
        ]);
        $this->buttonList->update('delete', 'label', __('Delete'));

        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndEdit', 'target' => '#edit_form']],
                ],
            ],
            -100
        );

        $this->buttonList->add(
            'new-button',
            [
                'label' => __('Save and New'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndNew', 'target' => '#edit_form'],
                    ],
                ],
            ],
            10
        );
        $selectSourceEvent = "";
        if ($this->storePickUpHelper->isMSISourceEnable() && !$this->_request->getParam('storepickup_id')) {
            /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
            $sourceRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
            $sources = $sourceRepository->getList()->getItems();
            $sourcesData = [];
            foreach ($sources as $source) {
                $sourcesData[$source->getSourceCode()] = [
                    'name' => $source->getName(),
                    'contact_name' => $source->getContactName(),
                    'email' => $source->getEmail(),
                    'description' => $source->getDescription(),
                    'latitude' => $source->getLatitude(),
                    'longitude' => $source->getLongitude(),
                    'country_id' => $source->getCountryId(),
                    'region_id' => $source->getRegionId(),
                    'region' => $source->getRegion(),
                    'city' => $source->getCity(),
                    'street' => $source->getStreet(),
                    'postcode' => $source->getPostcode(),
                    'phone' => $source->getPhone(),
                    'fax' => $source->getFax(),

                ];
            }
            $selectSourceEvent .= '
                var sourcesData = ' . json_encode($sourcesData) . '
                $("select#store_source_code").ready(function(){
                    $("select#store_source_code").change(function(el){
                        var sourceCode = el.target.value;
                        if(sourceCode) {
                            var sourceData = sourcesData[sourceCode];
                            if(sourceData) {
                                $("input#store_store_name").val(sourceData["name"]);
                                $("input#store_contact_name").val(sourceData["contact_name"]);
                                $("input#store_email").val(sourceData["email"]);
                                $("input#store_phone").val(sourceData["phone"]);
                                $("input#store_fax").val(sourceData["fax"]);
                                $("input#store_address").val(sourceData["street"]);
                                $("input#store_city").val(sourceData["city"]);
                                $("select#store_country_id").val(sourceData["country_id"]);
                                $("select#store_country_id").change();
                                setTimeout(function(){
                                    $("select#state_id").val(sourceData["region_id"]);
                                }, 100);
                                $("input#state").val(sourceData["region"]);
                                $("input#store_zipcode").val(sourceData["postcode"]);
                            }
                        }
                    });
                });
            ';
        }

        $this->_formScripts[] = '
            function toggleEditor() {
                if (tinyMCE.getInstanceById(\'store_content\') == null) {
                    tinyMCE.execCommand(\'mceAddControl\', false, \'store_content\');
                } else {
                    tinyMCE.execCommand(\'mceRemoveControl\', false, \'store_content\');
                }
            }


            require([
                    "jquery",
                    "underscore",
                    "mage/mage",
                    "mage/backend/tabs",
                    "domReady!"
                ], function($) {

                    var $form = $(\'#edit_form\');
                    $form.mage(\'form\', {
                        handlersData: {
                            saveOnly: {},
                            saveAndNew: {
                                action: {
                                    args: {back: \'new\'}
                                }
                            },
                            saveAndEdit: {
                                action: {
                                    args: {back: \'edit\'}
                                }
                            },
                        }
                    });
                    ' . $selectSourceEvent . '
                }
            );

        ';
    }
}
