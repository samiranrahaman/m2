<?php
namespace Servicesbanner\Gridpart3\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
class Background extends Column
{
    /** Url path */
    const BLOG_URL_PATH_EDIT = 'Gridpart3/template/edit';
    const BLOG_URL_PATH_DELETE = 'Gridpart3/template/delete';

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storemanager,
        array $components = [],
        array $data = [],
        $editUrl = self::BLOG_URL_PATH_EDIT
    ) {
        $this->_storeManager = $storemanager;
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       
        $backgroundModel = $objectManager->get('Servicesbanner\Gridpart3\Model\Theme\Background');
       
        
        
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {                
                $template = new \Magento\Framework\DataObject($item);
                $imageUrl =  $mediaDirectory.'Gridpart3/background/image'.$template->getBackground();                
                $item[$fieldName . '_src'] = $imageUrl;
                $item[$fieldName . '_alt'] = $template->getName();
                /* $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'Gridpart3/template/edit',
                    ['id' => $template->getGridpart2templateId(), 'store' => $this->context->getRequestParam('store')]
                ); */  
				 $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'Gridpart3/template/edit',
                    ['id' => $template->getId(), 'store' => $this->context->getRequestParam('store')]
                ); 
                $item[$fieldName . '_orig_src'] = $imageUrl;
            }
        }

        return $dataSource;
    }
}
