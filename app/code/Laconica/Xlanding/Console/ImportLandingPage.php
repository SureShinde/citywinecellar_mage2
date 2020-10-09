<?php

namespace Laconica\Xlanding\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Amasty\Xlanding\Api\Data\PageInterface;
use Amasty\Xlanding\Model\Page;

class ImportLandingPage extends Command
{
    const CWC_LANDING_PAGE_CSV_PATH = 'data/cwc_landing_page.csv';
    const TLS_LANDING_PAGE_CSV_PATH = 'data/tls_landing_page.csv';
    const CWC_ATTRIBUTE_CSV_PATH = 'data/cwc_attribute.csv';
    const CWC_STORE_ID = 1;
    const TLS_STORE_ID = 2;
    const CATALOG_PRODUCT_ENTITY_ID = 4;

    private $ruleAttributes = [];
    private $attributesValues = [];

    /**
     * @var \Magento\Framework\File\Csv $csvReader
     */
    private $csvReader;

    /**
     * @var \Magento\Framework\App\Utility\Files $files
     */
    private $files;

    /**
     * @var \Amasty\Base\Model\Serializer $serializer
     */
    private $serializer;

    /**
     * @var \Amasty\Xlanding\Model\RuleFactory $ruleFactory
     */
    private $ruleFactory;

    /**
     * @var \Amasty\Xlanding\Model\PageFactory $pageFactory
     */
    private $pageFactory;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    private $logger;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private $connection;

    /**
     * @var \Amasty\Xlanding\Api\PageRepositoryInterface $pageRepository
     */
    private $pageRepository;

    /**
     * @var \Magento\Eav\Setup\EavSetup $eavSetup
     */
    private $eavSetup;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     */
    private $eavAttribute;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\File\Csv $csvReader,
        \Magento\Framework\App\Utility\Files $files,
        \Amasty\Base\Model\Serializer $serializer,
        \Amasty\Xlanding\Model\PageFactory $pageFactory,
        \Amasty\Xlanding\Model\RuleFactory $ruleFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Amasty\Xlanding\Api\PageRepositoryInterface $pageRepository,
        \Magento\Eav\Setup\EavSetup $eavSetup,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        $name = null
    ) {
        parent::__construct($name);

        $this->logger = $logger;
        $this->csvReader = $csvReader;
        $this->files = $files;
        $this->serializer = $serializer;
        $this->pageFactory = $pageFactory;
        $this->ruleFactory = $ruleFactory;
        $this->pageRepository = $pageRepository;
        $this->connection = $resourceConnection->getConnection();
        $this->eavSetup = $eavSetup;
        $this->eavAttribute = $eavAttribute;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('la:import:landing-pages')
            ->setDescription('Import landing pages for both stores from m1');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $output->writeln("<info>Started:</info>");

        $processedCount = 0;
        $error = [];

        // Reading csv files
        $cwcLandingPages = $this->readCsvFile(self::CWC_LANDING_PAGE_CSV_PATH);
        $tlsLandingPages = $this->readCsvFile(self::TLS_LANDING_PAGE_CSV_PATH);
        $this->attributesValues = $this->readAttributeCsvFile(self::CWC_ATTRIBUTE_CSV_PATH);

        // CWC landing page import
        foreach ($cwcLandingPages as $item) {
            $result = $this->createLandingPage($item, self::CWC_STORE_ID);
            if (!$result) {
                array_push($error, $item['page_id']);
            }
            $processedCount++;
        }

        // TLS landing page import
        foreach ($tlsLandingPages as $item) {
            $result = $this->createLandingPage($item, self::TLS_STORE_ID);
            if (!$result) {
                array_push($error, $item['page_id']);
            }
            $processedCount++;
        }

        $output->writeln("<info>Processed pages: {$processedCount}</info>");

        // Error processing
        if (!empty($error)) {
            $list = implode(', ', $error);
            $output->writeln("<error>Error with: {$list}</error>");
        }

        $this->updateAttributesForXlanding();

        $end = gmdate("H:i:s", microtime(true) - $start);
        $output->writeln("<info>Finished in {$end}</info>");
    }

    /**
     * Setting attributes for using them in promo rules
     */
    private function updateAttributesForXlanding()
    {
        $attributes = array_unique($this->ruleAttributes);

        foreach ($attributes as $attribute) {
            if (!$attribute) {
                continue;
            }
            $attributeId = $this->eavAttribute->getIdByCode('catalog_product', $attribute);
            if (!$attributeId) {
                continue;
            }
            $this->eavSetup->updateAttribute(
                self::CATALOG_PRODUCT_ENTITY_ID,
                $attributeId,
                'is_used_for_promo_rules',
                1
            );
        }
    }

    /**
     * @param int $optionId
     * @return int|string
     */
    private function getCurrentOptionId(int $optionId){
        $value = isset($this->attributesValues[$optionId]) ? $this->attributesValues[$optionId] : '';

        if(!$value){
            return '';
        }

        $currentOptionIdSelect = $this->connection->select()
            ->from('eav_attribute_option_value', ['option_id'])
            ->joinLeft('eav_attribute_option', 'eav_attribute_option.option_id = eav_attribute_option_value.option_id', [])
            ->where('value LIKE ?', trim($value))
            ->where('store_id = ?', 0)
            ->where('attribute_id IS NOT NULL')
            ->order('eav_attribute_option_value.value_id DESC')
            ->limit(1);

        $optionId = $this->connection->fetchOne($currentOptionIdSelect);

        return $optionId;
    }

    /**
     * @param array $pageData
     * @param int $storeId
     * @return bool
     */
    private function createLandingPage(array $pageData, int $storeId)
    {
        $model = $this->pageFactory->create();
        $id = $this->checkLandingPageExist($pageData['page_id'], $storeId);

        $importData = $this->formatImportData($pageData, $storeId);
        $importData = $this->validateData($importData);

        if ($id) {
            $model->load($id);
        }

        $model->setData($importData);

        try {
            $model->save();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Check if landing page already exist in store
     *
     * @param int $oldId
     * @param int $storeId
     * @return string
     */
    private function checkLandingPageExist(int $oldId, int $storeId): string
    {
        $select = $this->connection->select()
            ->from('amasty_xlanding_page_store', ['page_id'])
            ->joinLeft('amasty_xlanding_page', 'amasty_xlanding_page_store.page_id = amasty_xlanding_page.page_id', [])
            ->where('amasty_xlanding_page.old_id = ?', $oldId)
            ->where('amasty_xlanding_page_store.store_id = ?', $storeId)
            ->limit(1);
        return $this->connection->fetchOne($select);
    }

    /**
     * @param array $data
     * @param int $storeId
     * @return array
     */
    private function formatImportData(array $data, int $storeId): array
    {
        $importData = [];

        if (!isset($data['identifier'], $data['title'], $data['page_id'])) {
            return $importData;
        }
        $importData['old_id'] = $data['page_id'];
        $importData['is_active'] = (isset($data['is_active'])) ? boolval($data['is_active']) : false;
        $importData['title'] = $data['title'];
        $importData['identifier'] = $data['identifier'];
        $importData['stores'] = [$storeId];
        $importData['page_layout'] = '2columns-left';
        $importData['layout_columns_count'] = 4;
        $importData['layout_include_navigation'] = 1;
        $importData['layout_static_top'] = (isset($data['layout_static_top']) && $data['layout_static_top'] !== "NULL") ? $data['layout_static_top'] : '';
        $importData['layout_static_top'] = (!$importData['layout_static_top'] && (isset($data['layout_description']) && $data['layout_description'] !== "NULL")) ? $data['layout_description'] : '';
        $importData['default_sort_by'] = (isset($data['default_sort_by']) && $data['default_sort_by'] !== "NULL") ? $data['default_sort_by'] : 'position';
        $importData['layout_update_xml'] = (isset($data['layout_update_xml']) && $data['layout_update_xml'] !== "NULL") ? $data['layout_update_xml'] : '';
        $importData['layout_file'] = '';

        $metaTitleStoreKey = 'meta_title_' . $storeId;
        $importData[$metaTitleStoreKey] = (isset($data['meta_title']) && $data['meta_title'] !== "NULL") ? $data['meta_title'] : '';

        $metaKeywordsStoreKey = 'meta_keywords_' . $storeId;
        $importData[$metaKeywordsStoreKey] = (isset($data['meta_keywords']) && $data['meta_keywords'] !== "NULL") ? $data['meta_keywords'] : '';

        $metaDescriptionStoreKey = 'meta_description_' . $storeId;
        $importData[$metaDescriptionStoreKey] = (isset($data['meta_description']) && $data['meta_description'] !== "NULL") ? $data['meta_description'] : '';

        $importRules = (isset($data['conditions_serialized'])) ? $this->serializer->unserialize($data['conditions_serialized']) : [];

        $importRules = $this->formatRuleConditions($importRules);

        if ($importRules) {
            $importData['rule'] = $importRules;
        }

        $importData['store_id'] = $storeId;
        $importData['dynamic_category_id'] = 0;

        return $importData;
    }

    /**
     * Format Rule condition data
     *
     * @param array $importRules
     * @return array
     */
    private function formatRuleConditions(array $importRules): array
    {
        $child = (isset($importRules['conditions']) && !empty($importRules['conditions'])) ? array_shift($importRules['conditions']) : [];

        if (!$this->getConditionType($importRules['type'])) {
            return [];
        }

        array_push($this->ruleAttributes, $importRules['attribute']);

        $rules = [
            'conditions' => [
                '1' => [
                    'type' => $this->getConditionType($importRules['type']),
                    'aggregator' => $importRules['aggregator'],
                    'value' => $importRules['value'],
                    'new_child' => ''
                ]
            ]
        ];

        if (!$child || !$this->getConditionType($child['type']) || $child['attribute'] === 'sale_type') {
            $rules['conditions']['1--1'] = [
                'type' => \Amasty\Xlanding\Model\Rule\Condition\Qty::class,
                'value' => 1,
                'operator' => '>='
            ];
            $rules['conditions']['1--2'] = [
                'type' => \Amasty\Xlanding\Model\Rule\Condition\Price\Sale::class,
                'value' => 1,
                'operator' => '=='
            ];
            return $rules;
        }

        array_push($this->ruleAttributes, $child['attribute']);

        $currentValues = $this->getCurrentValues($child['value'], $child['attribute']);

        $rules['conditions']['1--1'] = [
            'type' => $this->getConditionType($child['type']),
            'attribute' => $child['attribute'],
            'value' => $currentValues,
            'operator' => $child['operator']
        ];

        return $rules;
    }

    /**
     * Format attribute values from m1 to m2
     *
     * @param $values
     * @param $attribute
     * @return string
     */
    private function getCurrentValues($values, $attribute)
    {
        $validAttributes = ['dominantvar', 'sub_category', 'producer'];
        if (!in_array($attribute, $validAttributes)) {
            return $values;
        }
        $valuesArray = explode(',', $values);
        $currentValues = [];
        foreach ($valuesArray as $item) {
            $itemValue = $this->getCurrentOptionId($item);
            if (!$itemValue) {
                continue;
            }
            array_push($currentValues, $itemValue);
        }
        return implode(",", $currentValues);
    }

    /**
     * Validate and prepared data for import
     *
     * @param array $data
     * @return array
     */
    private function validateData(array $data)
    {
        if (isset($data['rule']) && isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];

            unset($data['rule']);

            $rule = $this->ruleFactory->create();
            $rule->loadPost($data);

            $data['conditions_serialized'] = $this->serializer->serialize($rule->getConditions()->asArray());
            unset($data['conditions']);
        }

        $metaData = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'meta_') !== false) {
                $metaData[substr($key, strripos($key, '_') + 1)][$key] = $value;
                unset($data[$key]);
            }
        }

        $data['meta_data'] = $this->serializer->serialize($metaData);

        if (($data[PageInterface::LANDING_IS_ACTIVE] ?? Page::STATUS_DISABLED) == Page::STATUS_DYNAMIC
            && !$data[PageInterface::DYNAMIC_CATEGORY_ID]
        ) {
            $data[PageInterface::LANDING_IS_ACTIVE] = Page::STATUS_ENABLED;
        }

        return $data;
    }

    /**
     * Return m2 rule condition class
     *
     * @param string $oldConditionType
     * @return string
     */
    private function getConditionType(string $oldConditionType): string
    {
        $conditionClass = '';
        $moduleClassArray = explode('/', $oldConditionType);

        if (empty($moduleClassArray)) {
            return $conditionClass;
        }

        $oldClassPath = explode("_", end($moduleClassArray));

        if (empty($oldClassPath)) {
            return $conditionClass;
        }

        $className = end($oldClassPath);
        $className = ($className) ? ucfirst($className) : '';

        $conditionClass = "Amasty\Xlanding\Model\Rule\Condition\\" . $className;

        return class_exists($conditionClass) ? $conditionClass : '';
    }

    /**
     * Return content of csv file
     *
     * @param string $filePath
     * @return array
     */
    private function readCsvFile(string $filePath): array
    {
        $this->csvReader->setDelimiter(';');
        $this->csvReader->setEnclosure('"');
        $fullPath = $this->files->getModuleFile('Laconica', 'Xlanding', $filePath);
        try {
            $csvItems = $this->csvReader->getData($fullPath);
        } catch (\Exception $e) {
            return [];
        }
        $headers = array_shift($csvItems);
        $items = [];
        foreach ($csvItems as $item) {
            array_push($items, array_combine($headers, $item));
        }
        return $items;
    }

    /**
     * Return m1 attributes from csv file
     *
     * @param string $filePath
     * @return array
     */
    private function readAttributeCsvFile(string $filePath): array
    {
        $this->csvReader->setDelimiter(';');
        $this->csvReader->setEnclosure('"');
        $fullPath = $this->files->getModuleFile('Laconica', 'Xlanding', $filePath);
        try {
            $csvItems = $this->csvReader->getData($fullPath);
        } catch (\Exception $e) {
            return [];
        }
        $headers = array_shift($csvItems);
        $items = [];
        foreach ($csvItems as $item) {
            $itemValue = array_combine($headers, $item);
            $items[$itemValue['option_id']] = $itemValue['value'];
        }
        return $items;
    }
}