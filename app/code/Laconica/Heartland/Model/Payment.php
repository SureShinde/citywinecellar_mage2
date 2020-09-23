<?php

namespace Laconica\Heartland\Model;

class Payment extends \HPS\Heartland\Model\Payment
{
    protected $_infoBlockType = \Laconica\Heartland\Block\Info\Cc::class;
}