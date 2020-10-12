<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\MigrateData\Controller\Adminhtml\Migratedata\Order\Ms\Product;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\System\Config\ProductSource;

/**
 * Class Save
 *
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Quotation
 */
class Migrate extends \Magento\Backend\App\Action
{
    
    public $order_info = array();
    public $order_item_info = array();
    public $order_item_flag = 0;
    
    public $store_id = 2;
    public $import_limit = 0;
    
    public $orderPrefix = 'TLS_MS_';
    public $website = 'tls';
    /**
     * Quotation grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {  
        /* generate product url rewrite*/
        //     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //     $categoryIds = [];
        //     $categoryFactory = $objectManager->create(\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory::class);
        //     $categories = $categoryFactory->create()                              
        //         ->addAttributeToSelect('*')
        //         ->addFieldToFilter('path', array('like'=> "1/93/%"));
        //     foreach ($categories as $cat){
        //         $categoryIds[$cat->getId()] = $cat;
        //     }
        //     // die('11');
        //     $productIds = [47089,47090,47091,47092,47093,47100,47107,47110,48256,48257,48258,48259,48260,48261,48262,48263,48264,48266,48268,48269,48270,48271,48273,48274,48275,48276,48277,48278,48279,48280,48281,48282,48283,48284,48285,48286,48287,48288,48289,48290,48291,48292,48293,48294,48295,48296,48297,48298,48299,48300,48301,48302,48303,48304,48305,48306,48307,48308,48309,48310,48311,48312,48313,48314,48315,48316,48317,48318,48319,48320,48321,48322,48323,48324,48326,48327,48328,48329,48330,48331,48332,48333,48334,48335,48336,48337,48338,48339,48341,48342,48353,48354,48358,48360,48362,48370,48372,48374,48376,48377,48383,48384,48386,48389,48390,48398,48400,48403,48405,48412,48415,48416,48418,48421,48422,48423,48424,48425,48426,48428,48429,48430,48431,48433,48434,48437,48440,48441,48443,48444,48445,48450,48453,48461,48476,48477,48485,48486,48489,48493,48495,48496,48498,48501,48503,48504,48510,48511,48512,48513,48517,48518,48519,48521,48522,48523,48524,48530,48531,48534,48536,48538,48539,48540,48541,48546,48550,48552,48554,48555,48556,48557,48561,48564,48565,48573,48578,48581,48582,48583,48584,48585,48586,48587,48588,48591,48592,48593,48594,48595,48596,48606,48610,48613,48615,48617,48618,48625,48626,48642,48646,48650,48651,48652,48656,48660,48667,48670,48671,48672,48673,48674,48676,48679,48680,48681,48682,48683,48685,48698,48701,48702,48709,48710,48713,48715,48716,48721,48726,48727,48728,48733,48734,48736,48737,48738,48739,48740,48741,48742,48743,48744,48745,48746,48747,48748,48749,48750,48751,48752,48753,48754,48755,48756,48757,48758,48759,48760,48761,48762,48763,48764,48765,48766,48767,48768,48769,48770,48771,48772,48773,48774,48775,48776,48777,48778,48779,48780,48781,48782,48783,48784,48785,48786,48787,48788,48789,48790,48791,48792,48793,48794,48795,48796,48797,48798,48799,48800,48801,48802,48803,48804,48805,48806,48807,48808,48809,48810,48811,48812,48813,48814,48815,48816,48817,48818,48819,48820,48821,48822,48823,48824,48825,48826,48827,48828,48829,48830,48831,48832,48833,48834,48835,48836,48837,48838,48839,48840,48841,48842,48843,48844,48845,48846,48847,48848,48849,48850,48851,48852,48853,48854,48855,48856,48857,48858,48859,48860,48861,48862,48863,48864,48865,48866,48867,48868,48869,48870,48871,48872,48873,48874,48875,48876,48877,48878,48879,48880,48881,48882,48883,48884,48885,48886,48887,48888,48889,48890,48891,48892,48893,48894,48895,48896,48897,48898,48899,48900,48901,48902,48903,48904,48905,48906,48907,48908,48909,48910,48911,48912,48913,48914,48915,48916,48917,48918,48919,48920,48921,48922,48923,48924,48925,48926,48927,48928,48929,48930,48931,48932,48933,48934,48935,48936,48937,48938,48939,48940,48941,48942,48943,48944,48945,48946,48947,48948,48949,48950,48951,48952,48953,48954,48955,48956,48957,48958,48959,48960,48961,48962,48963,48964,48965,48966,48967,48968,48969,48970,48971,48972,48973,48974,48975,48976,48977,48978,48979,48980,48981,48982,48983,48984,48985,48986,48987,48988,48989,48990,48991,48992,48993,48994,48995,48996,48997,48998,48999,49000,49001,49002,49003,49004,49005,49006,49007,49008,49009,49010,49011,49012,49013,49014,49015,49016,49017,49018,49019,49020,49021,49022,49023,49024,49025,49026,49027,49028,49029,49030,49031,49032,49033,49034,49035,49036,49037,49038,49039,49040,49041,49042,49043,49044,49045,49046,49047,49048,49049,49050,49051,49052,49053,49054,49055,49056,49057,49058,49059,49060,49061,49062,49063,49064,49065,49066,49067,49068,49069,49070,49071,49072,49073,49074,49075,49076,49077,49078,49079,49080,49081,49082,49083,49084,49085,49086,49087,49088,49089,49090,49091,49092,49093,49094,49095,49096,49097,49098,49099,49100,49101,49102,49103,49104,49105,49106,49107,49108,49109,49110];
        //     $error = [];
        //     foreach ($productIds as $productId) {
        //         try {
        //             $productUrlPathGenerator = $objectManager->create(\Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator::class);
        //             //$productId = 29734;
        //             $storeId = 2;
        //             $product = $objectManager->create(\Magento\Catalog\Model\Product::class)->load($productId);
        //             $productCategoryIds = [0];
        //             $productCategoryIds = array_merge($productCategoryIds, $product->getCategoryIds());
        //             foreach ($productCategoryIds as $productCategoryId) {
        //                 $create = false;
        //                 $category = null;
        //                 if ($productCategoryId == 0) {
        //                     $create = true;
        //                     $category = null;
        //                 } else {
        //                     if (isset($categoryIds[$productCategoryId]) && $categoryIds[$productCategoryId]) {
        //                         $category = $categoryIds[$productCategoryId];
        //                         $create = true;
        //                     }
        //                 }
        //                 if ($create) {
        //                     $metadata = '';
        //                     if ($category) {
        //                         if ($category) {
        //                             $metadata = ['category_id' => $category->getId()];
        //                         }
        //                     }
        //                     $requestPath = $productUrlPathGenerator->getUrlPathWithSuffix($product, $product->getStoreId(), $category);
        //                     $targetPath = $productUrlPathGenerator->getCanonicalUrlPath($product, $category);
        //                     $urlRewrite = $objectManager->create(\Magento\UrlRewrite\Model\UrlRewrite::class);
        //                     $urlRewrite->setEntityType('product')
        //                         ->setEntityId($productId)
        //                         ->setRequestPath($requestPath)
        //                         ->setTargetPath($targetPath)
        //                         ->setRedirectType(0)
        //                         ->setStoreId($storeId)
        //                         ->setDescription('')
        //                         ->setIsAutogenerated(1)
        //                         ->setMetadata($metadata)
        //                         ;
        //                     try {
        //                         $urlRewrite->save();
        //                     } catch (\Exception $e) {
        //                         $error[] = $productId;
        //                     }
        //                 }
        //             }
        //         } catch (\Exception $e) {
        //             // var_dump($productId);
        //             // var_dump($e->getMessage());
        //             // die();
        //             $error[] = $productId;
        //         }
        //     }
        //     var_dump($error);
        // die('211');

        // echo "<pre>";
        // var_dump($product->getStoreIds());
        // var_dump($product->getCategoryIds());
        

        // var_dump('getRequestPath');
        // var_dump($productUrlPathGenerator->getUrlPathWithSuffix($product, $product->getStoreId(), null));
        // var_dump('getTargetPath');
        // var_dump($productUrlPathGenerator->getCanonicalUrlPath($product, null));
        // die();
        

        // die();
        /* generate product url rewrite*/

        /*generate category url rewrite*/
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $categoryCollection = $objectManager->create(\Magento\Catalog\Model\ResourceModel\Category\Collection::class)
        // ->addAttributeToSelect('name')
        //     ->addAttributeToSelect('url_key')
        //     ->addAttributeToSelect('url_path');
        // foreach ($categoryCollection as $category) {
        //     // $category->save();
        //     // die();

        //     if ($category->getId() == 225) {
        //         $categoryModel = $objectManager->create(\Magento\Catalog\Model\Category::class)->load($category->getId());
        //         echo "<pre>";
        //         var_dump($category->getUrlKey());
        //         var_dump($category->getUrlPath());
        //         var_dump($category->getStoreId());
        //         var_dump($category->getName());
        //         $urlRewrite = $objectManager->create(\Magento\CatalogUrlRewrite\Model\Category\CurrentUrlRewritesRegenerator::class);
        //         $urlRewrite->generate($category->getStoreId(), $categoryModel);
        //         die('123');
        //     }
        // }
        // die('111');
        /*generate category url rewrite*/

        /** update visible pos **/
        // add default data for attribute
        // $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $eavAttribute = $this->objectManager->create(\Magento\Eav\Model\ResourceModel\Entity\Attribute::class);
        // $attributeId = $eavAttribute->getIdByCode('catalog_product', 'webpos_visible');
        // $action = $this->objectManager->create(\Magento\Catalog\Model\ResourceModel\Product\Action::class);
        // $connection = $action->getConnection();
        // $table = 'catalog_product_entity_int';
        // //set invisible for default
        // $productCollection = $this->objectManager->create(
        //     \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection::class
        // );
        // $visibleInSite = $this->objectManager->create(\Magento\Catalog\Model\Product\Visibility::class)
        //     ->getVisibleInSiteIds();

        // $productCollection->addAttributeToFilter('visibility', ['in' => $visibleInSite]);
        // $productCollection->addAttributeToFilter('type_id', 'simple');
        // // $version = $this->productMetadata->getVersion();
        // // $edition = $this->productMetadata->getEdition();
        // foreach ($productCollection->getAllIds() as $productId) {
        //         $data = [
        //             'attribute_id' => $attributeId,
        //             'store_id' => 0,
        //             'entity_id' => $productId,
        //             'value' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES
        //         ];
        //     $connection->insertOnDuplicate($table, $data, ['value']);
        // }
        // die('111');
        /** update visible pos **/

        $start = microtime(TRUE);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $csvProcessor = $objectManager->create(\Magento\Framework\File\Csv::class);
        $params = $this->getRequest()->getParams();
        $file = $this->getRequest()->getFiles('migrate_product');
        $importRawData = $csvProcessor->getData($file['tmp_name']);
        
        $fileFields = $importRawData[0];
        $validFields = $this->filterFileFields($fileFields);
        $invalidFields = array_diff_key($fileFields, $validFields);
        $importData = $this->filterImportData($importRawData, $invalidFields, $validFields);
        $success = 0;
        $skuCount = 0;
        $codeCount = 0;
        $nameCount = 0;
        /*michael test */

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    
        $resource = $objectManager->create(\Magento\Framework\App\ResourceConnection::class);
        $connection = $resource->getConnection();
        $table = $resource->getTableName('catalog_product_entity_varchar');

        $noExistCode = [];
        
        $newTable = 'catalog_product_entity';
        $selectQuery = 'SELECT `sku` from '. $newTable .' Where `type_id` = "simple";';

                // $selectQuery = 'SELECT `value_id` from '. $table .' Where `attribute_id` = 60 and `value` = \'' . $name .'\';';
                $result = $connection->fetchAll($selectQuery);
                // var_dump($result);
                foreach ($result as $r) {
                    $noExistCode[] = $r['sku'];
                }
                // if ($result) {
                //     $noExistCode[] = $result;
                // } else {
                //     // $noExistCode[] = $itemLookupCode;
                // }
                // var_dump($noExistCode);
                // var_dump($noExistCode);
        var_dump(implode(',', $noExistCode));
        die();
        foreach ($importData as $rowIndex => $dataRow) {
            // skip headers
            // if ($rowIndex == 0 || !$dataRow[2]) {
            //     continue;
            // }
            
            // if ($this->importProductMapping($dataRow)) {
            //     $success++;
            // }
            
            // if (isset($dataRow[10])) {
            //     var_dump($dataRow[10]);
            // }
            // if (isset($dataRow[19])) {
            //     var_dump($dataRow[19]);
            // }
            $success++;
            // if ((isset($dataRow[10]) && !$dataRow[10]) || !isset($dataRow[10])){
            //     $skuCount++;
            // }
            if ((isset($dataRow[0]) && !$dataRow[0]) || !isset($dataRow[0])) {
                $codeCount++;
            } else {
                if ($success < 15001) {
                    continue;
                }
                // $name = $dataRow[0];
                // $name = str_replace("'", "\'", $dataRow[0]);
                $itemLookupCode = str_replace('_migrateData', '', $dataRow[0]);
                // if (preg_match("/^\d+$/", $dataRow[19])) {
                // } else {
                //     continue;
                // }
                // if (is_string($dataRow[19])) {
                //     continue;
                // }
                // $dataRow[19] = str_replace('\'', '', $dataRow[19]);
                $selectQuery = 'SELECT `value_id` from '. $table .' Where `attribute_id` = 438 and `value` = \'' . $itemLookupCode .'\';';

                // $selectQuery = 'SELECT `value_id` from '. $table .' Where `attribute_id` = 60 and `value` = \'' . $name .'\';';
                $result = $connection->fetchOne($selectQuery);
                if ($result) {
                    
                } else {
                    $noExistCode[] = $itemLookupCode;
                }
            }
            // if ((isset($dataRow[48]) && !$dataRow[48]) || !isset($dataRow[48])) {
            //     $nameCount++;
            // }
            
            if ($success >= 20000) {
                var_dump($success);
                var_dump($skuCount);
                var_dump($codeCount);
                var_dump($nameCount);
                var_dump(count($noExistCode));
                echo "<br />";
                var_dump(implode(',', $noExistCode));
                die('3k');
            }
        }
        var_dump($success);
        var_dump($skuCount);
        var_dump($codeCount);
        var_dump($nameCount);
        var_dump(count($noExistCode));
        echo "<br />";
        var_dump(implode(',', $noExistCode));
        die('1233');

        /*michael test */

        foreach ($importData as $rowIndex => $dataRow) {
            // skip headers
            if ($rowIndex == 0 || !$dataRow[2]) {
                continue;
            }
            
            if ($this->importProductMapping($dataRow)) {
                $success++;
            }
        }
        $end = microtime(TRUE);
        echo "The code took " . ($end - $start) . " seconds to complete.";
        die('completed');
        var_dump($success);
        die();
    }
    
    public function getSalesPayment()
    {
        return array('method');
    }
    
    
    
    
    /**
     * Filter file fields (i.e. unset invalid fields)
     *
     * @param array $fileFields
     * @return string[] filtered fields
     */
    public function filterFileFields(array $fileFields)
    {
        $filteredFields = $this->getRequiredCsvFields();
        $requiredFieldsNum = count($this->getRequiredCsvFields());
        $fileFieldsNum = count($fileFields);
        
        // process title-related fields that are located right after required fields with store code as field name)
        for ($index = $requiredFieldsNum; $index < $fileFieldsNum; $index++) {
            $titleFieldName = $fileFields[$index];
            $filteredFields[$index] = $titleFieldName;
        }
        
        return $filteredFields;
    }
    
    
    /**
     * Get required columns
     *
     * @return array
     */
    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            0 => 'sku',
            1 => 'ms_id',
            2 => 'name'
        ];
    }
    
    /**
     * Modify import data
     *
     * @param array $rawData
     * @param array $invalidFields
     * @param array $validFields
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function filterImportData(array $rawData, array $invalidFields, array $validFields)
    {
        return $rawData;
        $validFieldsNum = count($validFields);
        foreach ($rawData as $rowIndex => $dataRow) {
            // skip empty rows
            if (count($dataRow) <= 1) {
                unset($rawData[$rowIndex]);
                continue;
            }
            // unset invalid fields from data row
            foreach (array_keys($dataRow) as $fieldIndex) {
                if (isset($invalidFields[$fieldIndex])) {
                    unset($rawData[$rowIndex][$fieldIndex]);
                }
            }
            // check if number of fields in row match with number of valid fields
            if (count($rawData[$rowIndex]) != $validFieldsNum) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file format.'));
            }
        }
        return $rawData;
    }
    
    public function convertDateTime($origDate)
    {
        $date = str_replace('/', '-', $origDate );
        $newDate = date("Y-m-d H:i", strtotime($date));
        
        return $newDate;
    }
    
    /**
     * Save supplier
     *
     * @param $dataRow
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function importProductMapping($dataRow)
    {
        $website = $this->website;
        $magento_product_sku = $dataRow[0];
        $ms_product_id = $dataRow[1];
        $product_name = $dataRow[2];
    
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    
        $resource = $objectManager->create(\Magento\Framework\App\ResourceConnection::class);
        $connection = $resource->getConnection();
        $table = $resource->getTableName('microsoft_product_mapping');
        $selectQuery = 'SELECT `id` from '. $table .' Where `magento_product_sku` = '. $magento_product_sku .' and `ms_product_id` = ' . $ms_product_id .';';
        $result = $connection->fetchOne($selectQuery);
        if ($result) {
            return true;
        }
        $query = 'INSERT INTO '.$table.'(
                `magento_product_sku`,
                `ms_product_id`,
                `product_name`)
            VALUES (
                \''.$magento_product_sku.'\',
                '.$ms_product_id.',
                \''. str_replace("'","''", $product_name). '\'
            );';
        $connection->query($query);
        
        return true;
    }
    
}
