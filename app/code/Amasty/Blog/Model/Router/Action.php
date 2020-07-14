<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\Router;

/**
 * Class
 */
class Action extends \Magento\Framework\DataObject
{
    /**
     * @var bool
     */
    private $isRedirect = false;

    /**
     * @var bool
     */
    private $redirectFlag;

    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var string
     */
    private $controllerName;

    /**
     * @var string
     */
    private $actionName;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var bool
     */
    private $result = false;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @param $alias
     *
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * @param $actionName
     *
     * @return $this
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @param $controllerName
     *
     * @return $this
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;

        return $this;
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @param $isRedirect
     *
     * @return $this
     */
    public function setIsRedirect($isRedirect)
    {
        $this->isRedirect = $isRedirect;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsRedirect()
    {
        return $this->isRedirect;
    }

    /**
     * @param $moduleName
     *
     * @return $this
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @param $result
     *
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return bool
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param $redirectFlag
     *
     * @return $this
     */
    public function setRedirectFlag($redirectFlag)
    {
        $this->redirectFlag = $redirectFlag;

        return $this;
    }
}
