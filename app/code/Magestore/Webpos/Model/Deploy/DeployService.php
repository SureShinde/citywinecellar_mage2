<?php

namespace Magestore\Webpos\Model\Deploy;

/**
 * Deploy webpos Service
 */
class DeployService implements \Magestore\Webpos\Api\Console\WebposDeployInterface
{
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $reader;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    /**
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    protected $driverInterface;
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $symfonyFileSystem;
    /**
     * @var \Composer\Util\Filesystem
     */
    protected $composerFileSystem;

    /**
     * DeployService constructor.
     *
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Module\Dir\Reader $reader
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Module\Dir\Reader $reader,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_dir = $dir;
        $this->reader = $reader;
        $this->filesystem = $filesystem;
    }

    /**
     * Execute
     *
     * @return $this
     * @throws \Exception
     */
    public function execute()
    {
        /** Deploy webapp */
        try {
            $sourceDir = $this->reader->getModuleDir('', 'Magestore_Webpos') . '/build/apps';
            if ($this->getDriverInterface()->isDirectory($sourceDir)) {
                //we will update/save source of this lib at the folder which includes media folder
                $mediaDir = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                    ->getAbsolutePath();
                $toolDir = $this->getDriverInterface()->getParentDirectory($mediaDir) . '/apps/';
                //delete old source of tool
                $this->rrmdir($toolDir);
                //copy new source of this tool
                $this->xcopy($sourceDir, $toolDir, 0775);
            }
        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Psr\Log\LoggerInterface::class)
                ->info($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Webpos Deploy
     *
     * @param string $deployName
     * @param string $output
     * @return null|string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function webposDeploy($deployName, $output)
    {
        try {
            $startTime = microtime(true);
            $result = $this->execute();
            $resultTime = microtime(true) - $startTime;
            $resultTime = round($resultTime, 2) . 's';
            $messageSuccess = $result ? "Webpos Deploy Successfully Completed!!" : "";
            $output->writeln($messageSuccess);
            $output->writeln('Execution time : ' . $resultTime);
            return null;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return ($e->getMessage());
        } catch (\Exception $e) {
            return ($e->getMessage());
        }
    }

    /**
     * Xcopy
     *
     * @param string $source
     * @param string $dest
     * @param int $permissions
     * @return bool
     */
    public function xcopy($source, $dest, $permissions = 0755)
    {
        // Check for symlinks
        if (is_link($source)) { // phpcs:ignore
            return $this->getDriverInterface()->symlink(
                $this->getSymfonyFileSystem()->readlink($source),
                $dest
            );
        }
        // Simple copy for a file
        if ($this->getDriverInterface()->isFile($source)) {
            return $this->getDriverInterface()->copy($source, $dest);
        }
        // Make destination directory
        if (!$this->getDriverInterface()->isDirectory($dest)) {
            $this->getDriverInterface()->createDirectory($dest, $permissions);
        }
        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            // Deep copy directories
            $this->xcopy("$source/$entry", "$dest/$entry", $permissions);
        }
        // Clean up
        $dir->close();
        return true;
    }

    /**
     * Rrm dir
     *
     * @param string $dir
     */
    public function rrmdir($dir)
    {
        if ($this->getDriverInterface()->isDirectory($dir)) {
            $objects = scandir($dir); // phpcs:ignore
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if ($this->getDriverInterface()->isDirectory($dir . "/" . $object)) {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        $this->getComposerFileSystem()->unlink($dir . "/" . $object);
                    }
                }
            }
            $this->getDriverInterface()->deleteDirectory($dir);
        }
    }

    /**
     * Get driver interface
     *
     * @return \Magento\Framework\Filesystem\DriverInterface
     */
    public function getDriverInterface()
    {
        if (!$this->driverInterface) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->driverInterface = $objectManager->get(\Magento\Framework\Filesystem\DriverInterface::class);
        }
        return $this->driverInterface;
    }

    /**
     * Get symfony file system
     *
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    public function getSymfonyFileSystem()
    {
        if (!$this->symfonyFileSystem) {
            $this->symfonyFileSystem = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Symfony\Component\Filesystem\Filesystem::class);
        }
        return $this->symfonyFileSystem;
    }

    /**
     * Get composer file system
     *
     * @return \Composer\Util\Filesystem
     */
    public function getComposerFileSystem()
    {
        if (!$this->composerFileSystem) {
            $this->composerFileSystem = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Composer\Util\Filesystem::class);
        }
        return $this->composerFileSystem;
    }
}
