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
namespace Custom\Chharo\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;

use Custom\Chharo\Api\Data\CategoryimagesInterfaceFactory;
use Custom\Chharo\Api\CategoryimagesRepositoryInterface;

use Magento\Catalog\Api\CategoryRepositoryInterface;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Custom Chharo Admin Categoryimages Controller
 */
abstract class Categoryimages extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $_resultPage;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var CategoryimagesInterfaceFactory
     */
    protected $categoryimagesDataFactory;

    /**
     * @var CategoryimagesRepositoryInterface
     */
    protected $_categoryimagesRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepositoryInterface;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * File Uploader factory.
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @param Context                                          $context
     * @param PageFactory                                      $resultPageFactory
     * @param ForwardFactory                                   $resultForwardFactory
     * @param CategoryimagesInterfaceFactory                   $categoryimagesDataFactory
     * @param CategoryimagesRepositoryInterface                $categoryimagesRepository
     * @param CategoryRepositoryInterface                      $categoryRepositoryInterface
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param Filesystem                                       $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime      $date
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        CategoryimagesInterfaceFactory $categoryimagesDataFactory,
        CategoryimagesRepositoryInterface $categoryimagesRepository,
        CategoryRepositoryInterface $categoryRepositoryInterface,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->categoryimagesDataFactory = $categoryimagesDataFactory;
        $this->_categoryimagesRepository = $categoryimagesRepository;
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_date = $date;
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_Chharo::categoryimages');
    }
}
