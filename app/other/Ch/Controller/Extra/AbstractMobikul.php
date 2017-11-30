<?php
/**
 * Custom Software.
 *
 * @category  Custom
 * @package   Custom_Chharo
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */

namespace Custom\Chharo\Controller\Extra;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\App\Emulation;

/**
 * Chharo API Catalog controller.
 */
abstract class AbstractChharo extends \Custom\Chharo\Controller\ApiController
{

    /**
     * $_mobileNotification
     *
     * @var \Magento\Chharo\Model\NotificationFactory
     */
    protected $_chharoNotification;

    /**
     * $_categoryfactory
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryfactory;

    /**
     * $_productFactory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * $_priceHelper
     *
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;

    /**
     * $_storeManager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * $_customerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * $_productStatus
     *
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_productStatus;

    /**
     * $_productVisibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_productVisibility;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        \Custom\Chharo\Helper\Catalog $helperCatalog,
        Emulation $emulate,
        \Custom\Chharo\Model\NotificationFactory $chharoNotification,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility
    ) {
    
        $this->_chharoNotification = $chharoNotification;
        $this->_categoryFactory = $categoryFactory;
        $this->_productFactory = $productFactory;
        $this->_priceHelper = $priceHelper;
        $this->_storeManager = $storeManager;
        $this->_logger = $logger;
        $this->_customerFactory = $customerFactory;
        $this->_productStatus = $productStatus;
        $this->_productVisibility = $productVisibility;
        parent::__construct($context, $helper, $helperCatalog, $emulate);
    }
}
