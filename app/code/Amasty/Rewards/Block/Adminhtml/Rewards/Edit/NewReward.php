<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Adminhtml\Rewards\Edit;

use Magento\Customer\Controller\RegistryConstants;

class NewReward extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry, $formFactory, $data);
        $this->setUseContainer(true);
    }

    /**
     * Form preparation
     *
     * @return void
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(['data' => ['id' => 'new_rewards_form', 'class' => 'admin__scope-old']]);
        $form->setUseContainer($this->getUseContainer());

        $customerId = (int)$this->getRequest()->getParam('id');

        $fieldset = $form->addFieldset('new_rewards_form_fieldset', []);

        $fieldset->addField('new_rewards_messages', 'note', []);

        $fieldset->addField(
            'new_rewards_action',
            'select',
            [
                'label' => __('Action'),
                'title' => __('Action'),
                'required' => true,
                'name' => 'new_rewards_action',
                'options' => [
                    'add' => __('Add'),
                    'deduct' => __('Deduct')
                ],
            ]
        );

        $fieldset->addField(
            'new_rewards_amount',
            'text',
            [
                'label' => __('Amount'),
                'title' => __('Amount'),
                'class' => 'validate-number validate-greater-than-zero',
                'required' => true,
                'name' => 'new_rewards_amount'
            ]
        );

        $fieldset->addField(
            'new_rewards_expiration_behavior',
            'select',
            [
                'label' => __('Points expiration behavior'),
                'title' => __('Points expiration behavior'),
                'required' => true,
                'name' => 'new_rewards_expiration_behavior',
                'options' => [
                    '0' => __('Never expire'),
                    '1' => __('Expire')
                ],
            ]
        );

        $fieldset->addField(
            'new_rewards_expiration_period',
            'text',
            [
                'label' => __('Points expiration period, days'),
                'title' => __('Points expiration period, days'),
                'required' => true,
                'class' => 'validate-number validate-zero-or-greater',
                'name' => 'new_rewards_expiration_period',
                'note' => __('If 0 is set, points will expire same day at midnight (12:00 am) your server time.')
            ]
        );

        $fieldset->addField(
            'new_rewards_comment',
            'textarea',
            [
                'label' => __('Comment'),
                'title' => __('Comment'),
                'required' => true,
                'name' => 'new_rewards_comment',
            ]
        );

        $fieldset->addField(
            'new_rewards_customer',
            'hidden',
            [
                'name' => 'new_rewards_comment',
                'value' => $customerId
            ]
        );

        $this->setForm($form);
    }

    /**
     * Attach new category dialog widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {

        $widgetOptions = $this->jsonEncoder->encode(
            [
                'saveCategoryUrl' => $this->getUrl('amasty_rewards/rewards/new'),
                'customerId' => $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
            ]
        );

        //TODO: JavaScript logic should be moved to separate file or reviewed
        return <<<HTML
<script>
require(["jquery","mage/mage", "Amasty_Rewards/js/add-points-dialog"],function($) {  
    // waiting for dependencies at first
    $(function(){ // waiting for page to load to have '#category_ids-template' available
        $('#add-points').newRewardsDialog($widgetOptions);
    });
});
</script>
HTML;
    }
}
