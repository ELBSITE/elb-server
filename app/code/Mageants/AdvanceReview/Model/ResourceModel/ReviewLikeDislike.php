<?php 
/**
 * @category Mageants AdvanceReview
 * @package Mageants_AdvanceReview
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvanceReview\Model\ResourceModel;

/**
 * Class ReviewLikeDislike
 * @package Mageants\AdvanceReview\Model\ResourceModel
 */
class ReviewLikeDislike extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	public function _construct(){
		$this->_init("review_like_dislike","likedislike_id");
	}
}
?>