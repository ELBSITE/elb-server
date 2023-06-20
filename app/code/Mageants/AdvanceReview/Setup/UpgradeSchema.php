<?php
/**
 * @category Mageants AdvanceReview
 * @package Mageants_AdvanceReview
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvanceReview\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package Mageants\AdvanceReview\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		
		$connection = $installer->getConnection();

		/**
         * upgrade table 'review_detail'
         */
		$connection->addColumn(
			$installer->getTable('review_detail'),
			'image_video',
			[
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'nullable' => false,
				'comment' => 'review image and video'
			]
		);

		$connection->addColumn(
            $installer->getTable('review_detail'),
            'verified_purchase',
            [
            	'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				'length' => 10,
				'nullable' => false,
				'comment' => 'verified purchase'
			]
        );
		$installer->endSetup();
	}
}