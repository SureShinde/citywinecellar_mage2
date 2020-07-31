<?php

namespace Laconica\Catalog\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    protected $_lineLength = 0;

    protected $_delimiter = ',';

    protected $_enclosure = '"';

    /**
     * @param $file
     * @return array
     * @throws Exception
     */
    public function getDataFromFile($file)
    {
        $data = [];
        $keyRowData = [];

        if (!file_exists($file)) {
            throw new Exception('File "' . $file . '" do not exists');
        }

        $fh = fopen($file, 'r');
        while ($rowData = fgetcsv($fh, $this->_lineLength, $this->_delimiter, $this->_enclosure)) {
            if (count($keyRowData) == 0) {
                foreach ($rowData as $key => $value) {
                    $keyRowData[$key] = $value;
                }
            } else {
                $newRowData = [];

                foreach ($rowData as $key => $value) {
                    $newRowData[$keyRowData[$key]] = $value;
                }

                $data[] = $newRowData;
            }
        }

        fclose($fh);
        return $data;
    }
}