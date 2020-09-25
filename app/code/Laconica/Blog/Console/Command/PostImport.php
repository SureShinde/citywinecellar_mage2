<?php

namespace Laconica\Blog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Amasty\Blog\Model\Source\PostStatus;
use Laconica\Blog\Helper\Config;
use Symfony\Component\Console\Input\InputOption;

class PostImport extends Command
{
    const RIT = 'remove-image-tls';
    const RIC = 'remove-image-cwc';
    const SHORT_CONTENT_LENGTH = 200;
    /**
     * @var \Magento\Framework\File\Csv $csvReader
     */
    private $csvReader;
    /**
     * @var \Magento\Framework\App\Utility\Files $files
     */
    private $files;
    /**
     * @var \Amasty\Blog\Api\Data\PostInterface $postFactory
     */
    private $postFactory;
    /**
     * @var \Amasty\Blog\Api\PostRepositoryInterface $postRepository
     */
    private $postRepository;
    /**
     * @var \Amasty\Blog\Model\ResourceModel\Posts\Collection $postCollectionFactory
     */
    private $postCollectionFactory;
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private $connection;

    private $oldCatToNewMap = [];
    private $tagMap = [];

    public function __construct(
        \Magento\Framework\File\Csv $csvReader,
        \Magento\Framework\App\Utility\Files $files,
        \Amasty\Blog\Api\Data\PostInterfaceFactory $postFactory,
        \Amasty\Blog\Api\PostRepositoryInterface $postRepository,
        \Amasty\Blog\Model\ResourceModel\Posts\CollectionFactory $postCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        string $name = null
    )
    {
        parent::__construct($name);
        $this->csvReader = $csvReader;
        $this->files = $files;
        $this->postFactory = $postFactory;
        $this->postRepository = $postRepository;
        $this->postCollectionFactory = $postCollectionFactory;

        $this->connection = $resourceConnection->getConnection();
    }

    protected function configure()
    {
        $this->setName('la:blog:post-import');
        $this->setDescription('Posts import from m1 sites');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('started');
        $this->start();
        $output->writeln("\nfinished");
    }

    /**
     * Main class function, witch starts import process
     */
    private function start()
    {
        $tlsPosts = $this->getTLSPostsInfo();
        $cwcPosts = $this->getCWCPostsInfo();

        echo "Clear Started";
        $posts = $this->postRepository->getPostCollection();
        foreach ($posts as $post) {
            $this->postRepository->delete($post);
        }
        echo "\nClear Finished";
        $this->importTlsPosts($tlsPosts);
        $this->importCWCPosts($cwcPosts);
    }

    /**
     * Import CWC posts
     * @param array $cwcPosts
     */
    private function importCWCPosts(array $cwcPosts)
    {
        $cwcPostsCategories = $this->getCWCPostsCategories();
        foreach ($cwcPosts as $post) {
            echo "\nProcessing Start: " . $post['title'];

            if (!$post['title'] || $post['title'] === 'NULL') {
                continue;
            }

            /** @var \Amasty\Blog\Api\Data\PostInterface $postEntity */
            $postEntity = $this->postFactory->create();

            $postExistId = $this->checkPostExist($post['post_id'], Config::CWC_STORE_ID);

            if ($postExistId) {
                $postEntity->setPostId($postExistId);
            }

            $postEntity->setTitle($post['title']);
            $postEntity->setMetaTitle($post['title']);
            $postEntity->setUrlKey($post['identifier']);

            if ($post['meta_description'] && $post['meta_description'] !== 'NULL') {
                $postEntity->setShortContent($post['meta_description']);
            } else {
                $shortContent = $this->getShortDescription($post['post_content']);
                $postEntity->setShortContent($shortContent);
            }

            if ($post['post_content'] && $post['post_content'] !== 'NULL') {
                $postEntity->setFullContent($post['post_content']);
            }

            if ($post['meta_description'] && $post['meta_description'] !== 'NULL') {
                $postEntity->setMetaDescription($post['meta_description']);
            }

            if ($post['meta_keywords'] && $post['meta_keywords'] !== 'NULL') {
                $postEntity->setMetaTags($post['meta_keywords']);
            }

            if ($post['created_time'] && $post['created_time'] !== 'NULL') {
                $postEntity->setCreatedAt($post['created_time']);
            }

            if ($post['update_time'] && $post['update_time'] !== 'NULL') {
                $postEntity->setUpdatedAt($post['update_time']);
            }

            if ($post['created_time'] && $post['created_time'] !== 'NULL') {
                $postEntity->setPublishedAt($post['created_time']);
            }

            $postEntity->setStatus(PostStatus::STATUS_ENABLED);
            $postEntity->setCommentsEnabled(1);
            $postEntity->setData(Config::OLD_ID_COLUMN, $post['post_id']);
            $processedPost = $this->postRepository->save($postEntity);

            // Post to store connection
            $this->connection->insertOnDuplicate(Config::AMASTY_BLOG_POST_STORES_TABLE, [
                'post_id' => $processedPost->getPostId(),
                'store_id' => Config::CWC_STORE_ID
            ]);

            // Post to category connection
            $postCategories = $cwcPostsCategories[$post['post_id']] ?? [];
            $categories = [];
            foreach ($postCategories as $postCategory) {
                $categoryId = $this->oldCatToNewMap[$postCategory] ?? null;
                if (!$categoryId) {
                    continue;
                }
                array_push($categories, [
                    'post_id' => $processedPost->getPostId(),
                    'category_id' => $categoryId
                ]);
            }
            if ($categories) {
                $this->connection->delete('amasty_blog_posts_category', [
                    'post_id = ? ' => $processedPost->getPostId()
                ]);
                $this->connection->insertMultiple('amasty_blog_posts_category', $categories);
            }

            // Tag to category connection
            $postTags = array_map('mb_strtolower', array_map('trim', explode(",", $post['tags'])));
            $tags = [];
            foreach ($postTags as $tag) {
                $tagId = $this->tagMap[$tag] ?? null;
                if (!$tagId) {
                    continue;
                }
                array_push($tags, [
                    'post_id' => $processedPost->getPostId(),
                    'tag_id' => $tagId
                ]);
            }
            if ($tags) {
                $this->connection->delete('amasty_blog_posts_tag', [
                    'post_id = ? ' => $processedPost->getPostId()
                ]);
                $this->connection->insertMultiple('amasty_blog_posts_tag', $tags);
            }
            echo "\nProcessing End: " . $post['title'];
        }
    }

    /**
     * Import TLS posts
     * @param array $tlsPosts
     */
    private function importTlsPosts(array $tlsPosts)
    {
        foreach ($tlsPosts as $post) {
            echo "\nProcessing Start: " . $post['title'];

            /** @var \Amasty\Blog\Api\Data\PostInterface $postEntity */
            $postEntity = $this->postFactory->create();
            $postExistId = $this->checkPostExist($post['post_id'], Config::TLS_STORE_ID);

            // If post exist, we'll update it
            if ($postExistId) {
                $postEntity->setPostId($postExistId);
            }

            if ($post['title'] && $post['title'] !== 'NULL') {
                $postEntity->setTitle($post['title']);
            }

            $postEntity->setUrlKey($post['url_key']);

            if ($post['short_content'] && $post['short_content'] !== 'NULL') {
                $postEntity->setShortContent($post['short_content']);
            } elseif ($post['meta_description'] && $post['meta_description'] !== 'NULL') {
                $postEntity->setShortContent($post['meta_description']);
            }

            if ($post['full_content'] && $post['full_content'] !== 'NULL') {
                $postEntity->setFullContent($post['full_content']);
            }

            if ($post['meta_title'] && $post['meta_title'] !== 'NULL') {
                $postEntity->setMetaTitle($post['meta_title']);
            }

            if ($post['meta_tags'] && $post['meta_tags'] !== 'NULL') {
                $postEntity->setMetaTags($post['meta_tags']);
            }

            if ($post['meta_description'] && $post['meta_description'] !== 'NULL') {
                $postEntity->setMetaDescription($post['meta_description']);
            }

            $postEntity->setCreatedAt($post['created_at']);
            $postEntity->setUpdatedAt($post['updated_at']);
            $postEntity->setPublishedAt($post['published_at']);
            $postEntity->setCommentsEnabled($post['comments_enabled']);

            if ($post['canonical_url'] && $post['canonical_url'] !== 'NULL') {
                $postEntity->setCanonicalUrl($post['canonical_url']);
            }

            $postEntity->setViews($post['views']);
            $postEntity->setStatus($post['status']);
            $postEntity->setData(Config::OLD_ID_COLUMN, $post['post_id']);
            $processedPost = $this->postRepository->save($postEntity);

            // Set post connection to store
            $this->connection->insertOnDuplicate(Config::AMASTY_BLOG_POST_STORES_TABLE, [
                'post_id' => $processedPost->getPostId(),
                'store_id' => Config::TLS_STORE_ID
            ]);

            echo "\nProcessing End: " . $post['title'];
        }
    }

    /**
     * Check for post exist by it's old ID and current store ID
     * @param int $oldId
     * @param int $storeId
     * @return string
     */
    private function checkPostExist(int $oldId, int $storeId)
    {
        $select = $this->connection->select()
            ->from('amasty_blog_posts', ['post_id'])
            ->join('amasty_blog_posts_store', "amasty_blog_posts_store.post_id = amasty_blog_posts.post_id", [])
            ->where(Config::OLD_ID_COLUMN . " = ?", (int)$oldId)
            ->where('store_id = ?', $storeId);
        return $this->connection->fetchOne($select);
    }

    /**
     * Returns array with TLS posts information from csv
     * @return array
     */
    private function getTLSPostsInfo(): array
    {
        $this->csvReader->setDelimiter(';');
        $this->csvReader->setEnclosure('"');
        $tlsPostsPath = "data/tls/mp_blog_posts.csv";
        $tlsPostsPath = $this->files->getModuleFile('Laconica', 'Blog', $tlsPostsPath);
        try {
            $tlsPosts = $this->csvReader->getData($tlsPostsPath);
        } catch (\Exception $e) {
            return [];
        }
        $tlsPostsHeader = array_shift($tlsPosts);
        $posts = [];
        foreach ($tlsPosts as $item) {
            array_push($posts, array_combine($tlsPostsHeader, $item));
        }
        return $posts;
    }

    /**
     * Returns array with CWC posts information from csv
     * @return array
     */
    private function getCWCPostsInfo(): array
    {
        $this->csvReader->setDelimiter(';');
        $this->csvReader->setEnclosure('"');
        $cwcPostsPath = "data/cwc/citywinecellar_blog.csv";
        $cwcPostsPath = $this->files->getModuleFile('Laconica', 'Blog', $cwcPostsPath);
        try {
            $cwcPosts = $this->csvReader->getData($cwcPostsPath);
        } catch (\Exception $e) {
            return [];
        }
        $cwcPostsHeader = array_shift($cwcPosts);
        $posts = [];
        foreach ($cwcPosts as $item) {
            array_push($posts, array_combine($cwcPostsHeader, $item));
        }
        $this->fillTagMap();
        return $posts;
    }

    /**
     * Returns array with CWC post to category connection from csv
     * @return array
     */
    private function getCWCPostsCategories(): array
    {
        $this->csvReader->setDelimiter(';');
        $this->csvReader->setEnclosure('"');
        $cwcPostsCategoriesPath = "data/cwc/citywinecellar_blog_post_cat.csv";
        $cwcPostsCategoriesPath = $this->files->getModuleFile('Laconica', 'Blog', $cwcPostsCategoriesPath);
        try {
            $cwcPostsCategories = $this->csvReader->getData($cwcPostsCategoriesPath);
        } catch (\Exception $e) {
            return [];
        }
        $cwcPostsCategoriesHeader = array_shift($cwcPostsCategories);
        $postsCategories = [];
        foreach ($cwcPostsCategories as $item) {
            array_push($postsCategories, array_combine($cwcPostsCategoriesHeader, $item));
        }
        $result = $categories = [];
        foreach ($postsCategories as $postsCategory) {
            $result[$postsCategory['post_id']][] = $postsCategory['cat_id'];
            $categories[$postsCategory['cat_id']] = $postsCategory['cat_id'];
        }
        $this->fillCatMap($categories);
        return $result;
    }

    /**
     * Generates short description from full
     * @param string $description
     * @return string
     */
    private function getShortDescription(string $description): string
    {
        $description = trim(strip_tags($description, 'p'));
        $firsPos = strpos($description, '.') + 1;
        $shortContentLength = (strlen($description) > $firsPos) ? strpos($description, '.', $firsPos) : self::SHORT_CONTENT_LENGTH;
        return ltrim(substr($description, 0, $shortContentLength), '&nbsp;') . ".";
    }

    /**
     * Filling catMap, we need it to connect post and category
     * @param array $categories
     */
    private function fillCatMap(array $categories)
    {
        $select = $this->connection->select()
            ->from('amasty_blog_categories', ['old_id', 'category_id'])
            ->where('old_id IN(?)', $categories);
        $this->oldCatToNewMap = $this->connection->fetchPairs($select);
    }

    /**
     * Filling tagMap, we need it to connect post and tag
     */
    private function fillTagMap()
    {
        $select = $this->connection->select()
            ->from('amasty_blog_tags', ['tag_id'])
            ->join('amasty_blog_tags_store', 'amasty_blog_tags.tag_id = amasty_blog_tags_store.tag_id', ['name']);
        $response = $this->connection->fetchAll($select);
        $result = [];
        foreach ($response as $item) {
            $tagName = mb_strtolower(trim($item['name']));
            $result[$tagName] = $item['tag_id'];
        }
        $this->tagMap = $result;
    }
}