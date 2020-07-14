<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Categories;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractMassAction
 */
abstract class AbstractMassAction extends \Amasty\Blog\Controller\Adminhtml\AbstractMassAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_Blog::categories';

    /**
     * @var \Amasty\Blog\Model\Repository\CategoriesRepository
     */
    private $repository;

    /**
     * @var \Amasty\Blog\Model\ResourceModel\Categories\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        \Amasty\Blog\Model\Repository\CategoriesRepository $repository,
        \Amasty\Blog\Model\ResourceModel\Categories\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter, $logger);
        $this->repository = $repository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Amasty\Blog\Model\ResourceModel\Categories\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return \Amasty\Blog\Model\Repository\CategoriesRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
