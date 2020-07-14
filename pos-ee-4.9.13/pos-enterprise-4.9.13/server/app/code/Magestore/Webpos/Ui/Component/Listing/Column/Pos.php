<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class \Magestore\Webpos\Ui\Component\Listing\Column\Pos
 */
class Pos implements OptionSourceInterface
{
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Pos\Pos\Collection
     */
    protected $collectionFactory;

    /**
     * Pos constructor.
     *
     * @param \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get pos labels array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $webposPosCollection = $this->collectionFactory->create();
        foreach ($webposPosCollection as $pos) {
            $options[$pos->getId()] = (string)$pos->getPosName();
        }
        return $options;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }

    /**
     * Get pos labels array for option element
     *
     * @return array
     */
    public function getOptions()
    {
        $results = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $results[] = ['value' => $index, 'label' => $value];
        }
        return $results;
    }
}
