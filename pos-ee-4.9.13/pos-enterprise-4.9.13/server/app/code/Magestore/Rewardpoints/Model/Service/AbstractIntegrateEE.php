<?php
/**
 * Created by PhpStorm.
 * User: duongdiep
 * Date: 19/02/2017
 * Time: 17:08
 */

namespace Magestore\Rewardpoints\Model\Service;

class AbstractIntegrateEE
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $cr;
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $config;
    /**
     * @var
     */
    protected $columns;
    /**
     * @var
     */
    protected $columns_convert;
    /**
     * @var
     */
    protected $update_items;
    /**
     * @var
     */
    protected $rewardHistoryFactory;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $modelManager;
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;
    /**
     * IntegrateEE constructor.
     * @param \Magento\Framework\App\ResourceConnection $cr
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $cr,
        \Magento\Config\Model\ResourceModel\Config $config,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $modelManager,
        \Magento\Framework\Escaper $escaper
    )
    {
        $this->cr = $cr;
        $this->config = $config;
        $this->modelManager = $modelManager;
        if($this->modelManager->isEnabled('Magento_Reward')) {
            $this->rewardHistoryFactory = $objectManager->create('Magento\Reward\Model\ResourceModel\Reward\History\Collection');
        }
        $this->escaper = $escaper;
    }
    public function getData($table){
        $connection = $this->cr->getConnection('core_write');
        $tableData = $this->cr->getTableName($table);
        $select = $connection->select()->from(array('main_table' => $tableData), array('*'));
        /* End SQL */
        $tableItems = $connection->query($select);
        $items = array();
        while ($row = $tableItems->fetch()) {
            $items[] = $row;
        }
        return $items;
    }
    public function getColumns($table){
        $connection = $this->cr->getConnection('core_write');
        $describeTable = $connection->describeTable($table);
        $columns = array();
        foreach($describeTable as $describe){
            $columns[] = $describe['COLUMN_NAME'];
        }
        return $columns;
    }
    public function startConvert($datas, $needChanges = [], $default = []){
        $this->update_items = [];
        $columns_convert = $this->columns_convert;
        array_shift($columns_convert);
        foreach($datas as $data){
            $item = array();
            foreach($columns_convert as $column){
                if(in_array($column, $this->columns)){
                    $item[$column] = $data[$column];
                }
            }
            if($needChanges){
                foreach($needChanges as $key => $value ){
                    $item[$key] = $item[$value];
                    unset($item[$value]);
                }
            }
            if($default){
                foreach($default as $key => $value ){
                    $item[$key] = $value;
                }
            }
            $item = $this->dynamicItem($item);
            $this->update_items[] = $item;
        }
    }
    public function dynamicItem($item){
        return $item;
    }

    public function inserttable($table){
        $resource = $this->cr;
        $tableinsert = $resource->getTableName($table);
        try{
            $connection = $this->cr->getConnection('core_write');
            $connection->beginTransaction();
            $connection->insertMultiple($tableinsert,$this->update_items);
            $connection->commit();
            return true;
        }catch (\Exception $e){
            $connection->rollBack();
        }
    }

    /**
    * Escape html entities
    *
    * @param string|array $data
    * @param array|null $allowedTags
    * @return string
    */
    public function escapeHtml($data, $allowedTags = null)
    {
        return $this->escaper->escapeHtml($data, $allowedTags);
    }
}