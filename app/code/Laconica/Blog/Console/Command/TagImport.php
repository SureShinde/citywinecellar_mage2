<?php

namespace Laconica\Blog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Amasty\Blog\Model\Source\CategoryStatus;
use Laconica\Blog\Helper\Config;

class TagImport extends Command
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
     * @var \Amasty\Blog\Api\Data\TagInterface $tagFactory
     */
    private $tagFactory;
    /**
     * @var \Amasty\Blog\Api\TagRepositoryInterface $tagRepository
     */
    private $tagRepository;
    /**
     * @var \Amasty\Blog\Model\ResourceModel\Tag\Collection $tagCollectionFactory
     */
    private $tagCollectionFactory;

    public function __construct(
        \Magento\Framework\File\Csv $csvReader,
        \Magento\Framework\App\Utility\Files $files,
        \Amasty\Blog\Api\Data\TagInterfaceFactory $tagFactory,
        \Amasty\Blog\Api\TagRepositoryInterface $tagRepository,
        \Amasty\Blog\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        string $name = null
    )
    {
        parent::__construct($name);
        $this->csvReader = $csvReader;
        $this->files = $files;
        $this->tagFactory = $tagFactory;
        $this->tagRepository = $tagRepository;
        $this->tagCollectionFactory = $tagCollectionFactory;
    }

    protected function configure()
    {
        $this->setName('la:blog:tag-import')
            ->setDescription('Tags import from m1 sites');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Started');
        $this->start();
        $output->writeln("\nFinished");
    }

    /**
     * Main class function, witch starts import process
     */
    private function start()
    {
        $tags = $this->getTagsInfo();

        foreach ($tags as $tag) {
            echo "\nProcessing Start: " . $tag['tag'];

            /** @var \Amasty\Blog\Api\Data\TagInterface $tagEntity */
            $tagEntity = $this->tagFactory->create();

            $tagName = trim($tag['tag']);
            $urlKey = $this->createUrlKey($tagName);

            // If Tag already exist, we'll update it
            $existEntity = $this->checkTagExist($urlKey);
            if ($existEntity) {
                $tagEntity->setTagId($existEntity->getTagId());
            }

            $tagEntity->setName($tagName);
            $tagEntity->setMetaTitle($tagName);

            $tagEntity->setUrlKey($urlKey);
            $tagEntity->setStoreId(Config::DEFAULT_STORE_ID);
            $tagEntity->setData(Config::OLD_ID_COLUMN, $tag['id']);
            $this->tagRepository->save($tagEntity);

            echo "\nProcessing End: " . $tag['tag'];
        }
    }

    /**
     * Create url key
     * @param $tag
     * @return string
     */
    private function createUrlKey($tag): string
    {
        $url = preg_replace('#[^0-9a-z]+#i', '-', $tag);
        $urlKey = strtolower($url);
        return $urlKey;
    }

    /**
     * Checking if Tag already exist, checking with url key to avoid duplication
     * @param $urlKey
     * @return \Magento\Framework\DataObject
     */
    private function checkTagExist($urlKey)
    {
        $collection = $this->tagCollectionFactory->create();
        $collection->addFieldToFilter(\Amasty\Blog\Api\Data\TagInterface::URL_KEY, $urlKey);
        $collection->setLimit(1);
        return $collection->getFirstItem();
    }

    /**
     * Returns tags information from csv
     * @return array
     */
    private function getTagsInfo(): array
    {
        $this->csvReader->setDelimiter(';');
        $this->csvReader->setEnclosure('"');
        $cwcTagsPath = "data/cwc/citywinecellar_blog_tags.csv";
        $cwcTagsPath = $this->files->getModuleFile('Laconica', 'Blog', $cwcTagsPath);
        try {
            $cwcTags = $this->csvReader->getData($cwcTagsPath);
        } catch (\Exception $e) {
            return [];
        }
        $cwcTagsHeader = array_shift($cwcTags);
        $tags = [];
        foreach ($cwcTags as $item) {
            array_push($tags, array_combine($cwcTagsHeader, $item));
        }
        return $tags;
    }
}