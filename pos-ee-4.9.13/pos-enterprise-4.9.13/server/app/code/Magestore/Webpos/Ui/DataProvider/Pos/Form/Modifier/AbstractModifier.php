<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\DataProvider\Pos\Form\Modifier;

/**
 * Class AbstractModifier
 * @package Magestore\Webpos\Ui\DataProvider\Pos\Form\Modifier
 */
class AbstractModifier extends \Magestore\Webpos\Ui\DataProvider\Form\Modifier\AbstractModifier
{
    /**
     * @var \Magestore\Webpos\Model\Pos\Pos
     */
    protected $currentPos;

    /**
     * @var string
     */
    protected $scopeName = 'webpos_pos_form.webpos_pos_form';

    /**
     * @return \Magestore\Webpos\Model\Pos\Pos|mixed
     */
    public function getCurrentPos() {
        if (!$this->currentPos)
            $this->currentPos = $this->registry->registry('current_pos');
        return $this->currentPos;
    }
}