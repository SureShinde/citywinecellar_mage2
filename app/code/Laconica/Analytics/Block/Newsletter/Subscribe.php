<?php

namespace Laconica\Analytics\Block\Newsletter;

class Subscribe extends \Laconica\Analytics\Block\Html\Gtm
{
    public function getSubscribeUrl()
    {
        return $this->getUrl('lanewsletter/newsletter/subscribe');
    }
}