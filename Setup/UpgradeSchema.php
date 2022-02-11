<?php

namespace Vuefront\Blog\Setup;

use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $connection = $installer->getConnection();

        $installer->startSetup();

        $installer->endSetup();
    }
}
