<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Ui\Component\Listing\Post;

use Magento\Framework\Data\OptionSourceInterface;

class Authors implements OptionSourceInterface
{
    /**
     * @var \Amasty\Blog\Api\AuthorRepositoryInterface
     */
    private $authorRepository;

    public function __construct(\Amasty\Blog\Api\AuthorRepositoryInterface $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $collection = $this->authorRepository->getAuthorCollection();
        foreach ($collection as $author) {
            $options[] = [
                'label' => $author->getName(),
                'value' => $author->getAuthorId()
            ];
        }

        return $options;
    }
}
