<?php

namespace Addsubscriptionplans\Gridpartsubscriptionplans\Setup;

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
            $gridpart2template = $installer->getConnection()->newTable(
            $installer->getTable('addsubscriptionplans'))
                ->addColumn(
                        'subscriptionplanid', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true), 'Code ID'
                )->addColumn(
                        'name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Name'
                )->addColumn(
                        'price', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Price'
                )->addColumn(
                        'timepired', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Timepired'
						)->addColumn(
                        'permonth', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Permonth'
						)->addColumn(
                        'perannually', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Perannually'
						)->addColumn(
                        'ongoingfee', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Ongoingfee'
						)->addColumn(
                        'onba', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Onba'
						)->addColumn(
                        'ed', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Ed'
						)->addColumn(
                        'ct', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Ct'
						)->addColumn(
                        'paa', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Paa'
						)->addColumn(
                        'alogo', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Alogo'
						)->addColumn(
                        'aypsp', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Aypsp'
						)->addColumn(
                        'elc', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Elc'
						)->addColumn(
                        'dmt', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Dmt'
						)->addColumn(
                        'cam', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Cam'
						)->addColumn(
                        'cvvc', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Cvvc'
						)->addColumn(
                        'crc', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Crc'
						)->addColumn(
                        'cd', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Cd'
						)->addColumn(
                        'ba', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(), 'Ba'
						)->addColumn(
                        'status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 4, array('nullable' => false, 'default' => 1), 'Status'
                );
           $installer->getConnection()->createTable($gridpart2template);
        $installer->endSetup();
    }

}
