<?php

namespace Magestore\Storepickup\Model\Config\Source;

/**
 * Class Email
 *
 * Used to create email source
 */
class Email implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magestore\Storepickup\Helper\Email
     */
    protected $email;

    /**
     * Email constructor.
     * @param \Magestore\Storepickup\Helper\Email $email
     */
    public function __construct(
        \Magestore\Storepickup\Helper\Email $email
    ) {
        $this->email = $email;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];
        $emailList = $this->email->getEmailList();
        foreach ($emailList as $key => $email) {
            $options[] = [
                'value' => $key,
                'label' => $email['name'] . ' (' . $email['email'] . ')'
            ];
        }
        return $options;
    }
}
