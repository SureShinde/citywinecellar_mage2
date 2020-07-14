<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Page\Product\Sorting;

use Amasty\Xlanding\Model\Page\Product\Sorting\SortInterface;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $className
     * @param array $data
     * @return SortInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($className, array $data = [])
    {
        $sortInstance = $this->objectManager->create('\Amasty\Xlanding\Model\Page\Product\Sorting\\'.$className, $data);

        if (!$sortInstance instanceof SortInterface) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('%1 doesn\'t implement SortInterface', $className)
            );
        }
        return $sortInstance;
    }
}
