<?php
namespace Magestore\Storepickup\Model\Config\Source;
/**
 * Class EmailTemplate
 * @package Magestore\Storepickup\Model\Config\Source
 */
class EmailTemplate extends \Magento\Config\Model\Config\Source\Email\Template
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $options[] =array(
            'value'=> 'none_email',
            'label' => __('None')
        );
        return $options;
    }
}
