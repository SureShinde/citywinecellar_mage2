<?php

namespace Laconica\Blog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Amasty\Blog\Model\Source\CategoryStatus;
use Laconica\Blog\Helper\Config;

class CategoryImport extends Command
{
    /**
     * @var \Magento\Framework\File\Csv $csvReader
     */
    private $csvReader;
    /**
     * @var \Magento\Framework\App\Utility\Files $files
     */
    private $files;
    /**
     * @var \Amasty\Blog\Api\Data\CategoryInterface $categoryFactory
     */
    private $categoryFactory;
    /**
     * @var \Amasty\Blog\Api\CategoryRepositoryInterface $categoryRepository
     */
    private $categoryRepository;
    /**
     * @var \Amasty\Blog\Model\ResourceModel\Categories\Collection $categoryCollectionFactory
     */
    private $categoryCollectionFactory;

    public function __construct(
        \Magento\Framework\File\Csv $csvReader,
        \Magento\Framework\App\Utility\Files $files,
        \Amasty\Blog\Api\Data\CategoryInterfaceFactory $categoryFactory,
        \Amasty\Blog\Api\CategoryRepositoryInterface $categoryRepository,
        \Amasty\Blog\Model\ResourceModel\Categories\CollectionFactory $categoryCollectionFactory,
        string $name = null
    )
    {
        parent::__construct($name);
        $this->csvReader = $csvReader;
        $this->files = $files;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    protected function configure()
    {
        $this->setName('la:blog:category-import')
            ->setDescription('Categories import from m1 sites');

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
        $categories = $this->getCategoriesInfo();
        foreach ($categories as $category) {

            echo "\nProcessing Start: " . $category['title'];

            /** @var \Amasty\Blog\Api\Data\CategoryInterface $categoryEntity */
            $categoryEntity = $this->categoryFactory->create();

            // If category exist we'll update it
            $existEntity = $this->checkCategoryExist($category['cat_id']);
            if ($existEntity) {
                $categoryEntity->setCategoryId($existEntity->getCategoryId());
            }

            $categoryEntity->setName($category['title']);
            $categoryEntity->setUrlKey($category['identifier']);
            $categoryEntity->setSortOrder($category['sort_order']);
            $categoryEntity->setMetaTitle($category['title']);
            $categoryEntity->setMetaDescription($category['meta_description']);
            $categoryEntity->setMetaTags($category['meta_keywords']);
            $categoryEntity->setStoreId(Config::DEFAULT_STORE_ID);
            $categoryEntity->setStatus(CategoryStatus::STATUS_ENABLED);
            $categoryEntity->setData(Config::OLD_ID_COLUMN, $category['cat_id']);
            $this->categoryRepository->save($categoryEntity);

            // Disable categories for TLS website
            $categoryEntity->setStoreId(Config::TLS_STORE_ID);
            $categoryEntity->setStatus(CategoryStatus::STATUS_DISABLED);
            $this->categoryRepository->save($categoryEntity);

            echo "\nProcessing End: " . $category['title'];
        }
    }

    /**
     * Check for category exist, buy it's old ID
     * @param int $categoryId
     * @return \Magento\Framework\DataObject
     */
    private function checkCategoryExist(int $categoryId)
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addFieldToFilter(Config::OLD_ID_COLUMN, $categoryId);
        $collection->setLimit(1);
        return $collection->getFirstItem();
    }

    /**
     * Returns array with category info from csv
     * @return array
     */
    private function getCategoriesInfo(): array
    {
        $this->csvReader->setDelimiter(';');
        $this->csvReader->setEnclosure('"');
        $cwcCategoriesPath = "data/cwc/citywinecellar_blog_cat.csv";
        $cwcCategoriesPath = $this->files->getModuleFile('Laconica', 'Blog', $cwcCategoriesPath);
        try {
            $cwcCategories = $this->csvReader->getData($cwcCategoriesPath);
        } catch (\Exception $e) {
            return [];
        }
        $cwcCategoriesHeader = array_shift($cwcCategories);
        $categories = [];
        foreach ($cwcCategories as $item) {
            array_push($categories, array_combine($cwcCategoriesHeader, $item));
        }
        return $categories;
    }

}