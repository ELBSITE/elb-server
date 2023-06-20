<?php 
/**
 * @category Mageants AdvanceReview
 * @package Mageants_AdvanceReview
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvanceReview\Model;

/**
 * Class ReviewLikeDislike
 * @package Mageants\AdvanceReview\Model
 */
class ReviewLikeDislike extends \Magento\Framework\Model\AbstractModel
{
	/**
     * Initialization
     *
     * @return void
     */
	public function _construct(){
		$this->_init("Mageants\AdvanceReview\Model\ResourceModel\ReviewLikeDislike");
	}
}
?>
