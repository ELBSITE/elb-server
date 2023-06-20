<?php 
/**
 * @category Mageants AdvanceReview
 * @package Mageants_AdvanceReview
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvanceReview\Model\ResourceModel\ReviewLikeDislike;

/**
 * Class Collection
 * @package Mageants\AdvanceReview\Model\ResourceModel\ReviewLikeDislike
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	/**
     * Define module
     *
     * @return void
     */
    public function _construct(){
		$this->_init("Mageants\AdvanceReview\Model\ReviewLikeDislike", "Mageants\AdvanceReview\Model\ResourceModel\ReviewLikeDislike");
	}

    /**
     * Add like filter
     *
     * @return $this
     */
    public function addLikeFilter()
    {
        $this->getSelect()->where('like_dislike = ?', 1);
        return $this;
    }

    /**
     * Add dislike filter
     *
     * @return $this
     */
    public function addDislikeFilter()
    {
        $this->getSelect()->where('like_dislike = ?', 0);
        return $this;
    }

    /**
     * Add reviewid filter
     *
     * @param int|string $reviewId
     * @return $this
     */
    public function addRevieIdFilter($reviewId)
    {
        $this->getSelect()->where('review_id = ?', $reviewId);
        return $this;
    }
}
?>