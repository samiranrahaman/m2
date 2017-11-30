<?php
/**
 * Custom Software.
 *
 * @category Custom
 *
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */

namespace Custom\Chharo\Helper;

use Custom\Chharo\Model\ChharoAuthFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Custom Chharo Helper Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Config\Model\Config\Backend\Encrypted
     */
    protected $_encrypted;

    /**
     * $_chharoAuthFactory.
     *
     * @var Custom\Chharo\Model\ChharoAuthFactory;
     */
    protected $_chharoAuthFactory;

    protected $_sessionManager;

    /**
     * @var Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * $_storeManager.
     *
     * @var \Magento\Store\Model\StoreManager
     */
    protected $_storeManager;

    protected $_apiSession;

    /**
     * @param Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Config\Model\Config\Backend\Encrypted $encrypted,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        ChharoAuthFactory $chharoAuthFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        DateTime $date,
        \Custom\Chharo\Model\ApiSession $apiSession
    ) {
        $this->_encrypted = $encrypted;
        $this->_chharoAuthFactory = $chharoAuthFactory;
        $this->_sessionManager = $sessionManager;
        $this->_storeManager = $storeManager;
        $this->_date = $date;
        $this->_apiSession = $apiSession;
        parent::__construct($context);
    }

    /**
     * getUsername api user name.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->scopeConfig->getValue(
            'chharo/authentication/username',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getPassword get api password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_encrypted->processValue(
            $this->scopeConfig->getValue(
                'chharo/authentication/password',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
    }

    /**
     * getTimeout get session timeout for api calls.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->scopeConfig->getValue(
            'chharo/authentication/session_timeout',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

     /**
     * getThemeCode get theme code
     *
     * @return int
     */
    public function getThemeCode()
    {
        return $this->scopeConfig->getValue(
            'chharo/authentication/theme_code',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getThemeCode get theme code
     *
     * @return int
     */
    public function getEnableLogging()
    {
        return $this->scopeConfig->getValue(
            'chharo/authentication/chharo_log',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getCanDebug can create log.
     *
     * @return int
     */
    public function getCanDebug()
    {
        return $this->getEnableLogging();
    }

    /**
     * getCurrentStore get current store id.
     *
     * @return int
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getStoreId();
    }

    public function getUrl($dir)
    {
        return $this->_storeManager->getStore()->getBaseUrl($dir);
    }

    public function getPageUrl($path, $params = [])
    {
        return $this->_storeManager->getStore()->getUrl($path, $params);
    }

    public function getConfigData(
        $path,
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE
    ) {
        return $this->scopeConfig->getValue(
            $path,
            $scope
        );
    }

    public function debugChharo($msg, $context)
    {
        if ($this->getCanDebug()) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $myLogger = $objectManager->get("\Custom\Chharo\Logger\ChharoLogger");
            $myLogger->debug($msg, $context);
        }
    }

    /**
     * authenticateApiCall authenticate api call.
     *
     * @param string $apiKey api key
     *
     * @return bool true|false
     */
    public function authenticateApiCall($apiKey = null)
    {
        try {
            /*
             * check if api key is not provided
             */
            if (!$apiKey && $apiKey == null) {
                return false;
            }

            return $this->_apiSession->isAuthenticRequest($apiKey);
        } catch (\Exception $e) {
            return false;
        }
    }
}
