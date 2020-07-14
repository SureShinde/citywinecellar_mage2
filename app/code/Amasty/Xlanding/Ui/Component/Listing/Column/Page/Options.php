<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Ui\Component\Listing\Column\Page;

use Magento\Framework\Data\OptionSourceInterface;

class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var \Amasty\Xlanding\Api\PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        \Amasty\Xlanding\Api\PageRepositoryInterface $pageRepository,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->pageRepository = $pageRepository;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->getPages();
        }
        return $this->options;
    }

    /**
     * @return array
     */
    private function getPages()
    {
        $options = [];

        foreach ($this->pageRepository->getEnabledList() as $page) {
                $options[] = ['label' => $page->getTitle(), 'value' => $page->getId()];
        }

        return $options;
    }
}
