<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Tags;

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
    const ADMIN_RESOURCE = 'Amasty_Blog::tags';

    /**
     * @var \Amasty\Blog\Model\Repository\TagRepository
     */
    private $repository;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        \Amasty\Blog\Model\Repository\TagRepository $repository
    ) {
        parent::__construct($context, $filter, $logger);
        $this->repository = $repository;
    }

    /**
     * @return \Amasty\Blog\Model\Repository\TagRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return \Amasty\Blog\Model\ResourceModel\Tag\Collection
     */
    public function getCollection()
    {
        return $this->repository->getTagCollection();
    }
}
