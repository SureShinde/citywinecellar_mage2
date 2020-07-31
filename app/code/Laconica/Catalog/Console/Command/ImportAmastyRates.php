<?php

namespace Laconica\Catalog\Console\Command;

use Exception;
use Laconica\Catalog\Helper\Data;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Filesystem\Io\File;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportAmastyRates extends Command
{
    protected $_lineLength = 0;

    protected $_delimiter = ',';

    protected $_enclosure = '"';

    protected $_files;

    protected $directoryList;

    protected $imageUploader;

    protected $file;

    protected $pageFactory;

    protected $logger;

    protected $categoryFactory;

    protected $fileHelper;

    protected $fileFactory;
    protected $filesystem;
    protected $directory;

    public function __construct(
        Files $files,
        File $file,
        DirectoryList $directoryList,
        \Magento\Catalog\Model\ImageUploader $imageUploader,
        PageFactory $pageFactory,
        LoggerInterface $logger,
        CategoryFactory $categoryFactory,
        Data $fileHelper,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_files = $files;
        $this->file = $file;
        $this->directoryList = $directoryList;
        $this->imageUploader = $imageUploader;
        $this->pageFactory = $pageFactory;
        $this->logger = $logger;
        $this->categoryFactory = $categoryFactory;
        $this->fileHelper = $fileHelper;
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        return parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('laconica:import:rates')
            ->setDescription('Import amasty rates');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $this->_files->getModuleFile('Laconica', 'Import', 'data/TLS.csv');
        $data = $this->fileHelper->getDataFromFile($file);
        $newData = [];

        foreach ($data as $key => $item) {
            $zipCodes = explode(',', $item['zip_from']);
            if (count($zipCodes) > 1) {
                foreach ($zipCodes as $zipCode) {
                    $newItem = $item;
                    $zipFromTo = explode('-', $zipCode);
                    $zipFrom = $zipFromTo[0];
                    $zipTo = isset($zipFromTo[1]) ? $zipFromTo[1] : $zipFromTo[0];
                    $newItem['zip_from'] = "$zipFrom";
                    $newItem['zip_to'] = "$zipTo";
                    $newData[] = $newItem;
                }
            } else {
                $newData[] = $item;
            }
        }
        $this->setDataToFile($newData);

        $output->writeln('<info>Import has finished.</info>');
    }

    public function setDataToFile($data)
    {
        $name = 'tls' . date('m_d_Y_H_i_s');
        $filepath = 'export/custom' . $name . '.csv';
        $this->directory->create('export');
        /* Open file */
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();

        /* Write Header */
        $stream->writeCsv(['country', 'state', 'city', 'zip_from', 'zip_to', 'weight_from', 'weight_to', 'price_from', 'price_to', 'qty_from', 'qty_to', 'frpp', 'name_delivery', 'shipping_type', 'rate', 'ppp', 'frpuw', 'estimated_delivery']);
        foreach ($data as $item) {
            $stream->writeCsv($item);
        }


        $content = [];
        $content['type'] = 'filename'; // must keep filename
        $content['value'] = $filepath;
        $content['rm'] = '0'; //remove csv from var folder

        $csvfilename = 'Product.csv';
        return $this->fileFactory->create($csvfilename, $content, DirectoryList::VAR_DIR);
    }

    /**
     * @param string $link
     * @param int $type
     * @return string|null
     */
    public function saveImage(string $link, int $type = 0)
    {
        try {
            $fileName = basename($link);
            $file = $this->file->read($link);
            $pathToFile = $this->directoryList->getPath(DirectoryList::TMP) . '/' . $fileName;
            if (!$this->file->write($pathToFile, $file)) {
                var_dump($link);
                die;
            }
            $_FILES['image'] = [
                'name' => $fileName,
                'tmp_name' => $pathToFile,
                'type' => mime_content_type($pathToFile),
                'size' => filesize($pathToFile),
                'error' => ''
            ];
            switch ($type) {
                case 0:
                    $this->imageUploader->setBaseTmpPath('cms/tmp/page');
                    $this->imageUploader->setBasePath('cms/page');
                    $result = $this->imageUploader->saveFileToTmpDir('image');
                    break;
                case 1:
                    $this->imageUploader->setBaseTmpPath('catalog/category');
                    $this->imageUploader->setBasePath('catalog/category');
                    $result = $this->imageUploader->saveFileToTmpDir('image');
                    break;
                default:
                    die($link);
            }
            $this->file->rm($pathToFile);
            return $result['name'];
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }
}
