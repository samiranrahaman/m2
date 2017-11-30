<?php
namespace Custom\Chharo\Block\Adminhtml\Bannerimage\Edit\Tab\View;

use Custom\Chharo\Controller\RegistryConstants;

/**
 * Adminhtml Banner image view information.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Info extends \Magento\Backend\Block\Template
{

    /**
     * Bannerimage
     *
     * @var \Custom\Chharo\Api\Data\BannerimageInterface
     */
    protected $bannerimage;

    /**
     * Bannerimage registry
     *
     * @var \Custom\Chharo\Model\Bannerimage
     */
    protected $bannerimageRegistry;

    /**
     * Bannerimage data factory
     *
     * @var \Custom\Chharo\Api\Data\BannerimageInterfaceFactory
     */
    protected $bannerDataFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Data object helper
     *
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context              $context
     * @param \Custom\Chharo\Api\Data\BannerimageInterfaceFactory $bannerDataFactory
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Framework\Api\DataObjectHelper              $dataObjectHelper
     * @param array                                                $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Custom\Chharo\Api\Data\BannerimageInterfaceFactory $bannerDataFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->bannerDataFactory = $bannerDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;

        parent::__construct($context, $data);
    }

    /**
     * Set Bannerimage registry
     *
     * @param      \Magento\Framework\Registry $coreRegistry
     * @return     void
     * @deprecated
     */
    public function setBannerimageRegistry(\Custom\Chharo\Model\BannerimageRegistry $bannerimageRegistry)
    {

        $this->bannerimageRegistry = $bannerimageRegistry;
    }

    /**
     * Get banner registry
     *
     * @return     \Custom\Chharo\Model\BannerimageRegistry
     * @deprecated
     */
    public function getBannerimageRegistry()
    {

        if (!($this->bannerimageRegistry instanceof \Custom\Chharo\Model\BannerimageRegistry)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Custom\Chharo\Model\BannerimageRegistry'
            );
        } else {
            return $this->bannerimageRegistry;
        }
    }

    /**
     * Retrieve banner object
     *
     * @return \Custom\Chharo\Api\Data\BannerimageInterface
     */
    public function getBannerimage()
    {
        if (!$this->bannerimage) {
            $this->bannerimage = $this->bannerDataFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $this->bannerimage,
                $this->_backendSession->getBannerimageData()['general'],
                '\Custom\Chharo\Api\Data\BannerimageInterface'
            );
        }
        return $this->bannerimage;
    }

    /**
     * Retrieve banner id
     *
     * @return string|null
     */
    public function getBannerimageId()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_BANNER_ID);
    }

    /**
     * Get banner creation date
     *
     * @return string
     */
    public function getCreateDate()
    {
        return $this->formatDate(
            $this->getBannerimage()->getCreatedTime(),
            \IntlDateFormatter::MEDIUM,
            true
        );
    }

    /**
     * Get banner's current status.
     *
     * @return string
     */
    public function getCurrentStatus()
    {

        return __('Online');
    }
}
