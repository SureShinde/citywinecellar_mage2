<?php
namespace Magestore\Storepickup\Model\Config\Source;

/**
 * Class EmailTemplate
 *
 * Used to create email template source
 */
class EmailTemplate extends \Magento\Config\Model\Config\Source\Email\Template
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $options[] = [
            'value' => 'none_email',
            'label' => __('None')
        ];
        return $options;
    }
}
