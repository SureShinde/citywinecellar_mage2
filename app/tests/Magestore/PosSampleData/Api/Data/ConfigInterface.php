<?php

namespace Magestore\PosSampleData\Api\Data;

interface ConfigInterface {
    const PATH = 'path';
    const VALUE = 'value';
    const SCOPE = 'scope';
    const SCOPE_ID = 'scope_id';


    /**
     * Get Path
     *
     * @return string|null
     */
    public function getPath();	
    /**
     * Set Path
     *
     * @param string|null $path
     * @return $this
     */
    public function setPath($path);

    /**
     * Get Value
     *
     * @return string|null
     */
    public function getValue();	
    /**
     * Set Value
     *
     * @param string|null $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Get Scope
     *
     * @return string|null
     */
    public function getScope();	
    /**
     * Set Scope
     *
     * @param string|null $scope
     * @return $this
     */
    public function setScope($scope);

    /**
     * Get Scope Id
     *
     * @return int|null
     */
    public function getScopeId();	
    /**
     * Set Scope Id
     *
     * @param int|null $scopeId
     * @return $this
     */
    public function setScopeId($scopeId);
}