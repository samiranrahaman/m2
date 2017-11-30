<?php
/**
 * Custom Software api session.
 *
 * @category Custom_Chharo
 *
 * @author    Custom
 * @copyright Copyright (c) 2010-2016 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */

namespace Custom\Chharo\Model;

/**
 * Api session model.
 *
 * @method                                         string getNoReferer()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApiSession extends \Magento\Framework\Session\SessionManager
{
    /**
     * @var Custom\Chharo\Helper\Data
     */
    protected $_apiHelper;

    /**
     * @throws \Magento\Framework\Exception\SessionException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Session\SaveHandlerInterface $saveHandler,
        \Magento\Framework\Session\ValidatorInterface $validator,
        \Magento\Framework\Session\StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\State $appState,
        \Custom\Chharo\Helper\Session $apiHelper
    ) {
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState
        );
        $this->_apiHelper = $apiHelper;
    }

    public function generateSessionId()
    {
        $sessionId = $this->getSessionId();

        return $sessionId;
    }

    public function isAuthenticRequest($sessionId)
    {
        $sessionKey = $this->getApiId();

        if ($sessionKey && $sessionId != null) {
            if ($sessionKey == $sessionId) {
                return 1;
            } else {
                return 0;
            }
        }

        return 0;
    }

    /**
     * Set api id.
     *
     * @param int|null $id
     *
     * @return $this
     */
    public function setApiId($id)
    {
        $this->storage->setData('api_id', $id);

        return $this;
    }

    /**
     * Retrieve api id from current session.
     *
     * @api
     *
     * @return int|null
     */
    public function getApiId()
    {
        if ($this->storage->getData('api_id')) {
            return $this->storage->getData('api_id');
        }

        return;
    }

    /**
     * Retrieve cookie lifetime.
     *
     * @return int
     */
    public function getCookieLifetime()
    {
        return $this->_apiHelper->getTimeout();
    }
}
