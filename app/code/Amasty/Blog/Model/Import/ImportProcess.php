<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\Import;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class ImportProcess
 */
class ImportProcess
{
    /**
     * @var array
     */
    private $imports;

    public function __construct($imports = [])
    {
        $this->imports = $imports;
    }
    
    public function processImport()
    {
        /** @var \Amasty\Blog\Model\Import\AbstractImport $import */
        foreach ($this->imports as $import) {
            $import->processImport();
        }
    }
}
