<?php
/**
 * @category Mageants AdvanceReview
 * @package Mageants_AdvanceReview
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvanceReview\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Mageants\AdvanceReview\Setup
 */ 
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup; 
        $installer->startSetup(); 
        
        if (!$installer->tableExists('review_like_dislike')) {
                /**
                 * Create table 'review_like_dislike'
                 */
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('review_like_dislike')
                )    
                ->addColumn(
                    'likedislike_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                    'identity'  => true,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    ],
                    'Like Dislike Id'
                )
                ->addColumn(
                    'review_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                    'nullable'  => false,
                    ],
                    'Review Id'
                )
                ->addColumn(
                    'customer_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                    'nullable'  => false,
                    ],
                    'Customer Id'
                )
                ->addColumn(
                    'like_dislike',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                    'nullable'  => false,
                    ],
                    'Review Like And Dislike'
                );
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
?>