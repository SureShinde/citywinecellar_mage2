<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Block\Adminhtml\Staff\Role\Edit\Tab\Permission;

/**
 * Class Form
 * @package Magestore\Appadmin\Block\Adminhtml\Staff\Role\Edit
 */
class Discount extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magestore\Appadmin\Model\Staff\Acl\AclRetriever
     */
    protected $_webposAclRetriever;

    public function _construct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_webposAclRetriever = $objectManager->get('\Magestore\Appadmin\Model\Staff\Acl\AclRetriever');
    }

    /**
     * Get discount value
     * @return float
     */
    public function getMaxDiscountPercent(){
        $rid = $this->_request->getParam('id', false);
        return $this->_webposAclRetriever->getMaxDiscountPercentByRole($rid);
    }
    public function toHtml()
    {
        return "<fieldset class='fieldset form-inline entry-edit'>
    <legend class='legend'>
        <span>" . __('General') . " </span>
    </legend><br />

    <div class='field'>
        <label class='label' for='all'><span>" . __('Maximum custom discount rate can apply (%)'). "</span></label>

         <div class='control'>
            <div class='admin__field-control control'>
                <input id='max_discount_percent' name='max_discount_percent' data-ui-id='appadmin-staff-role-edit-form-fieldset-element-text-name' value='". (float)$this->getMaxDiscountPercent(). "' class='input-text admin__control-text' maxlength='127' type='number' aria-required='true'>
                <p class='note'><span>Minimum: 0</span></p>
                <p class='note'><span>Maximum: 100</span></p>
            </div>
        </div>
    </div>

    </fieldset>
    <script>
        require([
            'jquery'
        ], function($){
            $('#max_discount_percent').blur(function(){
                let discount_percent = parseFloat($('#max_discount_percent').val());
                if(discount_percent < 0){
                    $('#max_discount_percent').val(0);
                }else if(discount_percent > 100){
                    $('#max_discount_percent').val(100);
                }else{
                     $('#max_discount_percent').val(discount_percent.toFixed(2)*1)
                }
            });
        });
    </script>";
    }
}
