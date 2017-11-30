<?php

namespace Managemarketplace\Gridpartmanage\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        $installer->startSetup();
            $Managemarketplace = $installer->getConnection()->newTable(
            $installer->getTable('managemarketplace'))
                ->addColumn(
                        'manage_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true), 'Code ID'
                )->addColumn(
                        'productbanner', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 4, array('nullable' => false, 'default' => 1), 'productbanner'
                );
           $installer->getConnection()->createTable($Managemarketplace);
        $installer->endSetup();
    }

}
