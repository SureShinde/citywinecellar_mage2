<?php
namespace Magestore\Webpos\Model\Deploy;
class DeployService implements \Magestore\Webpos\Api\Console\WebposDeployInterface
{
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $reader ;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    /**
     * DeployService constructor.
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Module\Dir\Reader $reader
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Module\Dir\Reader $reader,
        \Magento\Framework\Filesystem $filesystem
    )
    {
        $this->_dir = $dir;
        $this->reader = $reader;
        $this->filesystem = $filesystem;
    }
    /**
     * @return $this
     * @throws \Exception
     */
    public function execute()
    {
        /** deploy webapp */
        try {
            $sourceDir = $this->reader->getModuleDir('', 'Magestore_Webpos') . '/build/apps';
            if (is_dir($sourceDir)) {
                //we will update/save source of this lib at the folder which includes media folder
                $mediaDir = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                    ->getAbsolutePath();
                $toolDir = dirname($mediaDir).'/apps/';
                //delete old source of tool
                $this->rrmdir($toolDir);
                //copy new source of this tool
                $this->xcopy($sourceDir, $toolDir, 0775);
            }
        }catch (\Exception $e){
            var_dump($e->getMessage());
            return false;
        }
        return true;
    }
    /**
     * @param $indexName
     * @param $output
     * @return null|string
     */
    public function webposDeploy($deployName, $output){
        try {
            $startTime = microtime(true);
            $result = $this->execute();
            $resultTime = microtime(true) - $startTime;
            $resultTime = round($resultTime,2).'s';
            $messageSuccess = $result ? "Webpos Deploy Successfully Completed!!" : "";
            $output->writeln($messageSuccess);
            $output->writeln('Execution time : '.$resultTime);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return($e->getMessage());
        } catch (\Exception $e) {
            return($e->getMessage());
        }
    }
    /**
     * @param $source
     * @param $dest
     * @param int $permissions
     * @return bool
     */
    public static function xcopy($source, $dest, $permissions = 0755) {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }
        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }
        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
        }
        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            // Deep copy directories
            self::xcopy("$source/$entry", "$dest/$entry", $permissions);
        }
        // Clean up
        $dir->close();
        return true;
    }
    /**
     * @param $dir
     */
    public static function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object))
                        self::rrmdir($dir."/".$object);
                    else
                        unlink($dir."/".$object);
                }
            }
            rmdir($dir);
        }
    }
}