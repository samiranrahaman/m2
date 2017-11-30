<?php
namespace SR\CustomProductType\Model\Product\Type;

class CustomType extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    /**
     * Delete data specific for Custom product type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
    }
}