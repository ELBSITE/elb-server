<?php 
/**
 * @category Mageants AdvanceReview
 * @package Mageants_AdvanceReview
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvanceReview\Helper;

use Mageants\AdvanceReview\Model\ReviewLikeDislikeFactory;

/**
 * Class Data
 * @package Mageants\AdvanceReview\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	
    /**
     * @var \Mageants\AdvanceReview\Model\ReviewLikeDislikeFactory
     */
    protected $_reviewLikeDislike;

    /**
     * Review constructor
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Mageants\AdvanceReview\Model\ReviewLikeDislikeFactory $reviewLikeDislike
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        ReviewLikeDislikeFactory $reviewLikeDislike
    )
    {
        parent::__construct($context);
        $this->_reviewLikeDislike = $reviewLikeDislike;
    }


    /**
     * @return bool|string
     */
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
                $config_path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
    }

    /**
     * Return total like
     *
     * @return int|string
     */
    public function getLikeCount($reviewId)
    {
        $likeCollection=$this->_reviewLikeDislike->create()->getCollection()->addRevieIdFilter($reviewId)->addLikeFilter();
        
        return count($likeCollection->getData());  
    }

    /**
     * Return total dislike
     *
     * @return int|string
     */
    public function getDislikeCount($reviewId)
    {
        $dislikeCollection=$this->_reviewLikeDislike->create()->getCollection()->addRevieIdFilter($reviewId)->adddislikeFilter();
        
        return count($dislikeCollection->getData());
    }
}
