<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Setup;

use Amasty\Blog\Api\Data\PostInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Amasty\Blog\Model\ResourceModel\Posts as PostsResource;
use Amasty\Blog\Model\ResourceModel\Author as AuthorResource;
use Amasty\Blog\Api\Data\AuthorInterface;

/**
 * Class
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Amasty\Blog\Api\AuthorRepositoryInterface
     */
    private $authorRepository;

    public function __construct(
        \Magento\Framework\App\State $appState,
        \Amasty\Blog\Api\AuthorRepositoryInterface $authorRepository
    ) {
        $this->appState = $appState;
        $this->authorRepository = $authorRepository;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Exception
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->appState->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_ADMINHTML,
            [$this, 'upgradeCallback'],
            [$setup, $context]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function upgradeCallback(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.7.2', '<')) {
            $this->linkPostAuthor($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param $setup
     */
    private function linkPostAuthor(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $tableName = $setup->getTable(PostsResource::TABLE_NAME);
        if ($connection->tableColumnExists($tableName, PostInterface::POSTED_BY)) {
            $select = $connection->select()->from($setup->getTable(PostsResource::TABLE_NAME));
            $select->group(PostInterface::POSTED_BY);
            $authorIdByName = [];
            foreach ($connection->fetchAll($select) as $postData) {
                $author = $this->authorRepository->createAuthor(
                    $postData[PostInterface::POSTED_BY],
                    $postData[AuthorInterface::FACEBOOK_PROFILE],
                    $postData[AuthorInterface::TWITTER_PROFILE]
                );
                $authorIdByName[$postData[PostInterface::POSTED_BY]] = $author->getAuthorId();
            }

            $select = $connection->select()->from(
                $setup->getTable(PostsResource::TABLE_NAME),
                [PostInterface::POST_ID, PostInterface::POSTED_BY]
            );
            $dataForInsert = [];
            foreach ($connection->fetchAll($select) as $postData) {
                $authorId = isset($authorIdByName[$postData[PostInterface::POSTED_BY]])
                    ? $authorIdByName[$postData[PostInterface::POSTED_BY]] : null;
                $dataForInsert = [
                    PostInterface::POST_ID => $postData[PostInterface::POST_ID],
                    AuthorInterface::AUTHOR_ID => $authorId
                ];
            }
            $connection->insertOnDuplicate(
                $setup->getTable(PostsResource::TABLE_NAME),
                $dataForInsert,
                [AuthorInterface::AUTHOR_ID]
            );
        }
    }
}
