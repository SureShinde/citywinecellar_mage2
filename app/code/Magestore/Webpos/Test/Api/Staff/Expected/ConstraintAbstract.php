<?php


namespace Magestore\Webpos\Test\Api\Staff\Expected;


abstract class ConstraintAbstract
{

    /**
     * @param string $json
     * @return array
     */
    public function getKeysFromJsonString($json = '{}') {
        return array_keys(json_decode($json, true));
    }

    /**
     * @return array
     */
    public function getSchema() {
        return $this->getKeysFromJsonString($this->getSchemaJson());
    }

    /**
     * @return string
     */
    abstract public function getSchemaJson();

}