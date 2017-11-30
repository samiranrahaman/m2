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
class Token extends \Magento\Framework\App\Helper\AbstractHelper
{
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

    /**
     * $_objectManager.
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * $_androidTokenFactory.
     *
     * @var \Custom\Chharo\Model\AndroidTokenFactory
     */
    protected $_androidToken;

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
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Custom\Chharo\Model\AndroidTokenFactory $androidTokenFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_androidToken = $androidTokenFactory;
        parent::__construct($context);
    }

    public function setAndroidToken($customerId, $token)
    {
        try {
            $androidTokenModel = $this->_androidToken->create();
            if ($customerId != '' && $token != '') {
                $collection = $androidTokenModel->getCollection()->addFieldToFilter('token', $token);
                if ($collection->getSize() > 0) {
                    foreach ($collection as $eachRow) {
                        $this->_androidToken->create()->load($eachRow->getId())->setCustomerId($customerId)->save();

                        return $eachRow->getId();
                    }
                } else {
                    return $this->_androidToken->create()->setToken($token)->setCustomerId($customerId)->save()->getId();
                }
            }
            if ($customerId == '' && $token != '') {
                $collection = $androidTokenModel->getCollection()->addFieldToFilter('token', $token);
                if ($collection->getSize() > 0) {
                    foreach ($collection as $eachRow) {
                        $this->_androidToken->create()->load($eachRow->getId())->setCustomerId($customerId)->save();

                        return $eachRow->getId();
                    }
                } else {
                    return $this->_androidToken->create()->setToken($token)->setCustomerId($customerId)->save()->getId();
                }
            }
        } catch (\Exception $e) {
            $this->_objectManager->get("\Custom\Chharo\Helper\Data")->debugChharo("Exception in ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
        }
    }
}
