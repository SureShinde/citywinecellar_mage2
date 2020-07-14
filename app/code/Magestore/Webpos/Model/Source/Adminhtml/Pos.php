<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Source\Adminhtml;
class Pos implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magestore\Webpos\Api\Pos\PosRepositoryInterface
     */
    public $posRepository;

    /**
     * Pos constructor.
     * @param \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository
     */
    public function __construct(
        \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository
    ){
        $this->posRepository = $posRepository;
    }


    /**
     * @return array
     */
    public function toOptionArray()
    {
        $allPos = $this->posRepository->getAllPos();
        $allPosArray = [];
        foreach ($allPos as $pos) {
            $allPosArray[] = ['label' => $pos->getPosName(), 'value' => $pos->getPosId()];
        }
        return $allPosArray;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $allPos = $this->posRepository->getAllPos();
        $allPosArray = [];
        foreach ($allPos as $pos) {
            $allPosArray[$pos->getPosId()] = $pos->getPosName();
        }
        return $allPosArray;
    }

}