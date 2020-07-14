<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Helper;

/**
 * Class File
 * @package Magestore\ReportSuccess\Helper
 */
class File extends \Magento\Backup\Helper\Data
{
    /**
     * Extracts information from backup's filename
     *
     * @param string $filename
     * @return \Magento\Framework\DataObject
     */
    public function extractDataFromFilename($filename)
    {
        $extensions = $this->getExtensions();

        $filenameWithoutExtension = $filename;

        foreach ($extensions as $extension) {
            $filenameWithoutExtension = preg_replace(
                '/' . preg_quote($extension, '/') . '$/',
                '',
                $filenameWithoutExtension
            );
        }

        $filenameWithoutExtension = substr($filenameWithoutExtension, 0, strrpos($filenameWithoutExtension, "."));

        $fileNameArray = explode("_", $filenameWithoutExtension);

        foreach ($fileNameArray as $string){
            $time = $string;
        }

        $name = str_replace('_' . $time, '', $filenameWithoutExtension);


        $extension = str_replace($filenameWithoutExtension. '.', '', $filename);


        $result = new \Magento\Framework\DataObject();

        $result->addData(['name' => $filename, 'display_name' => $name, 'type' => 'tgz', 'time' => strtotime($time), 'extension' => $extension]);

        return $result;
    }
}