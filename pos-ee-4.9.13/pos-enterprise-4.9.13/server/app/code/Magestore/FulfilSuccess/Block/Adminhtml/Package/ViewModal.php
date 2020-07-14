<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\Package;

class ViewModal extends \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\DetailHolder
{

    /**
     *
     * @return string@return string
     */
    public function getTitle()
    {
        return __('Package Information');
    }

    /**
     *
     * @return string
     */
    public function getModalId()
    {
        return 'package_detail_holder';
    }

}
