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

use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Custom Chharo Helper Catalog.
 */
class Session extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * __construct.
     *
     * @param \Magento\Framework\App\Helper\Context              $context
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\ObjectManagerInterface          $objectManager
     * @param DateTime                                           $date
     * @param \Magento\Framework\Filesystem\DirectoryList        $dir
     * @param \Magento\Framework\Image\Factory                   $imageFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * getTimeout get session timeout for api calls
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
}
