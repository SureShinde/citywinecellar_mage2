<?php
namespace Magestore\Storepickup\Model\Config\Source;
/**
 * Class Email
 * @package Magestore\Storepickup\Model\Config\Source
 */
class Email implements \Magento\Framework\Option\ArrayInterface
{
    protected $email;

    public function __construct(
        \Magestore\Storepickup\Helper\Email $email
    ){
        $this->email = $email;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = [];
        $emailList = $this->email->getEmailList();
        foreach ($emailList as $key=>$email){
            $options[] =array(
                'value'=> $key,
                'label' => $email['name']. ' (' . $email['email']. ')'
            );
        }
        return $options;
    }
}
