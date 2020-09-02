<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Model\Locator;

use Magento\Framework\Registry;
use Magento\Backend\Model\Session;

/**
 * Class RegistryLocator
 */
class RegistryLocator implements LocatorInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry,
        Session $session
    ) {
        $this->registry = $registry;
        $this->session = $session;
    }

    /**
     * @param string
     * @return mixed
     */
    public function getSession($key) {
        return $this->session->getData($key, null);
    }

    /**
     * @param string string
     * @return
     */
    public function setSession($key, $data)
    {
        $this->session->setData($key, $data);
    }

    /**
     * @param string string
     * @return
     */
    public function unsetSession($key)
    {
        $this->setSession($key, null);
    }

}
