<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Staff;
/**
 * Class StaffManagement
 * @package Magestore\Webpos\Model\Staff
 */
class StaffManagement implements \Magestore\Webpos\Api\Staff\StaffManagementInterface
{

    /**
     * const is_sharing_account
     */
    const IS_SHARING_ACCOUNT = 1;
    /**
     * @var \Magestore\Webpos\Api\Data\Staff\Login\LoginResultInterface
     */
    protected $loginResult;
    /**
     * @var \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
     */
    protected $staffRepository;
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $sessionManagerInterface;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var StaffFactory
     */
    protected $staffFactory;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var \Magestore\Webpos\Api\Staff\SessionRepositoryInterface
     */
    protected $sessionRepository;
    /**
     * @var \Magestore\Webpos\Api\Location\LocationRepositoryInterface
     */
    protected $locationRepository;
    /**
     * @var \Magestore\Webpos\Api\Pos\PosRepositoryInterface
     */
    protected $posRepository;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magestore\Webpos\Api\Data\Staff\Logout\LogoutResultInterface
     */
    protected $logoutResult;
    /**
     * @var AuthorizationRuleFactory
     */
    protected $authorizationRuleCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManagerment;


    /**
     * @var \Magestore\Appadmin\Api\Event\DispatchServiceInterface
     */
    protected $dispatchService;

    /**
     * @var \Magestore\WebposIntegration\Api\ApiServiceInterface
     */
    protected $apiService;

    /**
     * StaffManagement constructor.
     * @param \Magestore\Webpos\Api\Data\Staff\Login\LoginResultInterface $loginResult
     * @param \Magestore\Webpos\Api\Data\Staff\Logout\LogoutResultInterface $logoutResult
     * @param \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magestore\Appadmin\Model\Staff\StaffFactory $staffFactory
     * @param \Magestore\Webpos\Api\Data\Staff\SessionInterface $session
     * @param \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository
     * @param \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
     * @param \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Appadmin\Model\ResourceModel\Staff\AuthorizationRule\CollectionFactory $authorizationRuleCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerment
     * @param \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService
     * @param \Magestore\WebposIntegration\Api\ApiServiceInterface $apiService
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Staff\Login\LoginResultInterface $loginResult,
        \Magestore\Webpos\Api\Data\Staff\Logout\LogoutResultInterface $logoutResult,
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Psr\Log\LoggerInterface $logger,
        \Magestore\Appadmin\Model\Staff\StaffFactory $staffFactory,
        \Magestore\Webpos\Api\Data\Staff\SessionInterface $session,
        \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository,
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository,
        \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Appadmin\Model\ResourceModel\Staff\AuthorizationRule\CollectionFactory $authorizationRuleCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManagerment,
        \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService,
        \Magestore\WebposIntegration\Api\ApiServiceInterface $apiService
    )
    {
        $this->loginResult = $loginResult;
        $this->logoutResult = $logoutResult;
        $this->staffRepository = $staffRepository;
        $this->sessionManagerInterface = $sessionManager;
        $this->timezone = $timezone;
        $this->logger = $logger;
        $this->staffFactory = $staffFactory;
        $this->session = $session;
        $this->sessionRepository = $sessionRepository;
        $this->locationRepository = $locationRepository;
        $this->posRepository = $posRepository;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->coreRegistry = $coreRegistry;
        $this->authorizationRuleCollectionFactory = $authorizationRuleCollectionFactory;
        $this->storeManagerment = $storeManagerment;
        $this->dispatchService = $dispatchService;
        $this->apiService = $apiService;
    }

    /**
     * Login Staff
     * {@inheritdoc}
     */
    public function login($staff)
    {
        $token = $this->getAccessToken();
        $username = $staff->getUsername();
        $password = $staff->getPassword();
        if ($username && $password) {
            try {
                $resultLogin = $this->staffFactory->create()->authenticate($username, $password);
                if ($resultLogin && $resultLogin->getStatus() == \Magestore\Appadmin\Api\Data\Staff\StaffInterface::STATUS_ENABLED && $resultLogin->getRoleId()) {
                    $this->sessionManagerInterface->regenerateId();
                    $sessionId = $this->sessionManagerInterface->getSessionId();
                    $this->session->setStaffId($resultLogin->getId());
                    $this->session->setSessionId($sessionId);
                    $this->session->setLoggedDate(strftime('%Y-%m-%d %H:%M:%S', $this->timezone->scopeTimeStamp()));
                    $this->sessionRepository->save($this->session);
                    $roleId = $resultLogin->getRoleId();
                    $authenticationRule = $this->authorizationRuleCollectionFactory->create()
                        ->addFieldToFilter('role_id', $roleId)
                        ->addFieldToFilter('resource_id', array('in' => array('Magestore_Appadmin::all', 'Magestore_Webpos::manage_pos')));
                    if (!$authenticationRule->getSize()) {
                        throw new \Magento\Framework\Exception\AuthorizationException(__('Your account is invalid. Please try again'));
                    }
                    $this->loginResult->setToken($token);
                    $this->loginResult->setSessionId($sessionId);
                    $this->loginResult->setTimeout($this->scopeConfig->getValue('webpos/general/session_timeout'));
                    $this->loginResult->setStaffId($resultLogin->getId());
                    $this->loginResult->setStaffName($resultLogin->getName());
                    $this->loginResult->setMessage(__('Success'));
                    $this->loginResult->setWebsiteId($this->storeManagerment->getWebsite()->getId());

                    /* sharing pos config */
                    $expireTime = $this->getExpireTime();
                    $sharingAccountConfig = $this->scopeConfig->getValue('webpos/security/sharing_acount');
                    $listCurrentSessionWithPos = $this->sessionRepository->getListByStaffId($resultLogin->getId());
                    $listCurrentSessionWithPos->addFieldToFilter(\Magestore\Webpos\Api\Data\Staff\SessionInterface::POS_ID, array('gt' => 0));
                    $listCurrentSessionWithPos->addFieldToFilter(\Magestore\Webpos\Api\Data\Staff\SessionInterface::LOGGED_DATE, array('from' => $expireTime));
                    $hasOccupyPos = $listCurrentSessionWithPos->getSize();
                    $this->loginResult->setSharingAccount(self::IS_SHARING_ACCOUNT);
                    /* if sharing config = No and staff are processing on Pos */
                    if ($sharingAccountConfig != self::IS_SHARING_ACCOUNT && $hasOccupyPos > 0) {
                        $this->loginResult->setSharingAccount(!self::IS_SHARING_ACCOUNT);
                    }
                    /* if sharing account */
                    if ($this->loginResult->getSharingAccount() == self::IS_SHARING_ACCOUNT) {
                        // get list available location
                        $locations = $this->locationRepository->getListLocationWithStaff($resultLogin->getId());
                        if (!$locations) {
                            throw new \Magento\Framework\Exception\LocalizedException(__('No POS available'));
                        }
                        $this->loginResult->setLocations($locations);
                    }
                    return $this->loginResult;
                } else {
                    throw new \Magento\Framework\Exception\AuthorizationException(
                        __('Your account is invalid. Please try again'),
                        new \Exception(),
                        \Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_CANNOT_LOGIN);
                }
            } catch (\Magento\Framework\Exception\AuthorizationException $e) {
                throw new \Magento\Framework\Exception\AuthorizationException(
                    __('Your account is invalid. Please try again'),
                    new \Exception(),
                    \Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_CANNOT_LOGIN
                );
            }
        }
        return $this->loginResult;
    }


    /**
     * continue Login Staff
     * {@inheritdoc}
     */
    public function continueLogin()
    {
        $token = $this->getAccessToken();
        $sessionId = $this->request->getParam(\Magestore\WebposIntegration\Controller\Rest\RequestProcessor::SESSION_PARAM_KEY);
        try {
            $sessionLogin = $this->sessionRepository->getBySessionId($sessionId);
            if ($sessionLogin) {
                $listCurrentSessionWithPos = $this->sessionRepository->getListByStaffId($sessionLogin->getStaffId());
                $listCurrentSessionWithPos->addFieldToFilter(\Magestore\Webpos\Api\Data\Staff\SessionInterface::SESSION_ID, array('neq' => $sessionId));
                /* delete list $session */
                if ($listCurrentSessionWithPos->getSize() > 0) {
                    foreach ($listCurrentSessionWithPos as $session) {
                        // delete current staff on POS  and force sign out.
                        if ($session->getPosId()) {
                            $pos = $this->posRepository->getById($session->getPosId());
                            $this->dispatchService->dispatchEventForceSignOut($pos->getStaffId(), $pos->getPosId());
                            $pos->setStaffId(null)->save();
                            $this->sessionRepository->signOutPos($pos->getPosId());
                        }
                    }
                }
                // get list available location
                $locations = $this->locationRepository->getListLocationWithStaff($sessionLogin->getStaffId());
                if (!$locations) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('No POS available'));
                }
                $this->loginResult->setToken($token);
                $this->loginResult->setSessionId($sessionId);
                $this->loginResult->setLocations($locations);
                $this->loginResult->setTimeout($this->scopeConfig->getValue('webpos/general/session_timeout'));
                $this->loginResult->setMessage(__('Success'));
                $this->loginResult->setSharingAccount(self::IS_SHARING_ACCOUNT);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Cannot Continue to Login !'));
        }
        return $this->loginResult;
    }

    /**
     * @param string $resource
     * @param int $staffId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isAllowResource($resource, $staffId)
    {
        $allPermission = $this->sessionRepository->getAllCurrentPermission($staffId);
        if (in_array('Magestore_Appadmin::all', $allPermission)) {
            return true;
        }
        if (in_array($resource, $allPermission)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @param string $phpSession
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function authorizeSession($phpSession)
    {
        $webposModel = $this->sessionRepository->getBySessionId($phpSession);
        if (!$this->coreRegistry->registry('currrent_webpos_staff')) {
            $staff = $this->staffRepository->getById($webposModel->getStaffId());
            $this->coreRegistry->register('currrent_webpos_staff', $staff);
        }

        if (!$this->coreRegistry->registry('currrent_session_model')) {
            $this->coreRegistry->register('currrent_session_model', $webposModel);
        }
        if ($webposModel->getId()) {

            $logTimeStaff = $webposModel->getData('logged_date');
            $currentTime = $this->timezone->scopeTimeStamp();
            $logTimeStamp = strtotime($logTimeStaff);
            if (($currentTime - $logTimeStamp) <= $this->scopeConfig->getValue('webpos/general/session_timeout')) {
                $newLoggedDate = strftime('%Y-%m-%d %H:%M:%S', $this->timezone->scopeTimeStamp());
                $webposModel->setData('logged_date', $newLoggedDate);
                $this->sessionRepository->save($webposModel);
                return $webposModel->getStaffId();
            } else {
                if ($webposModel->getPosId()) {
                    $this->posRepository->freePosById($webposModel->getPosId());
                }
                $this->sessionRepository->delete($webposModel);

                throw new \Magento\Framework\Exception\AuthorizationException(
                    __(\Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_MESSAGE),
                    new \Exception(),
                    \Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_FORCE_SIGN_OUT
                );
            }
        } else {
            return 0;
        }
    }


    /**
     * Logout Staff
     * {@inheritdoc}
     */
    public function logout()
    {
        $sessionId = $this->request->getParam(\Magestore\WebposIntegration\Controller\Rest\RequestProcessor::SESSION_PARAM_KEY);
        try {
            $sessionLogin = $this->sessionRepository->getBySessionId($sessionId);
            if ($sessionLogin) {
                /* get $expireTime */
                $expireTime = $this->getExpireTime();
                /**/
                $listCurrentSessionWithPos = $this->sessionRepository->getListByStaffId($sessionLogin->getStaffId());
                $listCurrentSessionWithPos->addFieldToFilter(\Magestore\Webpos\Api\Data\Staff\SessionInterface::POS_ID, $sessionLogin->getPosId());
                $listCurrentSessionWithPos->addFieldToFilter(\Magestore\Webpos\Api\Data\Staff\SessionInterface::LOGGED_DATE, array('from' => $expireTime));

                /** @var \Magestore\Webpos\Model\Staff\Session $sessionWithPos */
                foreach ($listCurrentSessionWithPos as $sessionWithPos) {
                    /* delete current staff on POS */
                    if ($sessionLogin->getPosId() === $sessionWithPos->getPosId()) {
                        $pos = $this->posRepository->getById($sessionLogin->getPosId());
                        $pos->setStaffId(null)->save();
                    }
                }

                $sessionLogin->delete($sessionLogin);
                $this->logoutResult->setMessage(__('Logout Success!'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Cannot logout!'));
        }
        return $this->logoutResult;
    }

    /**
     *
     */
    public function getExpireTime()
    {
        $currentTimeStamp = $this->timezone->scopeTimeStamp();
        $expireTimeStamp = $currentTimeStamp - $this->scopeConfig->getValue('webpos/general/session_timeout');
        $expireTime = date('Y-m-d h:i:s', $expireTimeStamp);
        return $expireTime;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\AuthorizationException
     */
    public function getAccessToken()
    {
        try {
            $token = $this->apiService->getToken();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\AuthorizationException(__("Can't retrieve access token, please get technical support."));
        }
        return $token;
    }
}