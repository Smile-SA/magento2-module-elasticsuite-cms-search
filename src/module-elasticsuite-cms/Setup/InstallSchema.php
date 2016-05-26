<?php
/**
 * Smile_ElasticSuiteCms install schema
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile_ElasticSuiteCms
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2016 Smile
 */
namespace Smile\ElasticsuiteCms\Setup;

use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Eav\Setup\EavSetup;

/**
 * Install Schema for Training Seller
 *
 * @package   Smile\Seller\Setup
 * @copyright 2016 Smile
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * Installs DB schema for the module
     *
     * @param SchemaSetupInterface   $setup   The setup interface
     * @param ModuleContextInterface $context The module Context
     *
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $table      = $setup->getTable('cms_page');

        // Append a column 'is_searchable' into the db.
        $connection->addColumn(
            $table,
            'is_searchable',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default'  => '0',
                'comment'  => 'If cms page is searchable',
            ]
        );

        $setup->endSetup();
    }
}
