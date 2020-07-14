<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Appadmin\Model\Staff\Acl;

class LabelResources
{
    /**
     * Root label resources
     *
     * @var array
     */
    protected $labelResources;

    /**
     * @param array $labelResources
     */
    public function __construct($labelResources = [])
    {
        $this->labelResources = $labelResources;
    }

    /**
     * Retrieve label resources
     *
     * @return array
     */
    public function getLabelResources()
    {
        return $this->labelResources;
    }
}
