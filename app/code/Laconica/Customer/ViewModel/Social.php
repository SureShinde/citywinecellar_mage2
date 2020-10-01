<?php

namespace Laconica\Customer\ViewModel;

class Social implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Amasty\SocialLogin\Model\SocialData $socialData
     */
    private $socialData;

    /**
     * @var \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    private $jsonHelper;

    public function __construct(
        \Amasty\SocialLogin\Model\SocialData $socialData,
        \Magento\Framework\Serialize\Serializer\Json $jsonHelper
    ) {
        $this->socialData = $socialData;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Returns amasty enabled socials in json
     * @return string
     */
    public function getEnabledSocials(): string
    {
        return $this->jsonHelper->serialize(['socials' => $this->socialData->getEnabledSocials()]);
    }
}