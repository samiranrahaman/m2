<?php

namespace Custom\Chharo\Model\ApiSession;

class Storage extends \Magento\Framework\Session\Storage
{
    /**
     * @param \Magento\Customer\Model\Config\Share       $configShare
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param string                                     $namespace
     * @param array                                      $data
     */
    public function __construct(
        \Magento\Customer\Model\Config\Share $configShare,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $namespace = 'chharo',
        array $data = []
    ) {
        parent::__construct($namespace, $data);
    }
}
