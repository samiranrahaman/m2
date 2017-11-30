<?php
namespace SR\CustomProductType\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class InstallData
 *
 * @package 
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
         $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'yourcustomattribute_id',/* Custom Attribute Code */
            [
                'group' => 'Product Details',/* Group name in which you want 
                                              to display your custom attribute */
                'type' => 'int',/* Data type in which formate your value save in database*/
                'backend' => '',
                'frontend' => '',
                'label' => 'Your Custom Attribute Label', /* lablel of your attribute*/
                'input' => 'select',
                'class' => '',
                'source' => 'SR\CustomProductType\Model\Config\Source\Options',
                                /* Source of your select type custom attribute options*/
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                                    /*Scope of your attribute */
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false
            ]
        );
    }
}
