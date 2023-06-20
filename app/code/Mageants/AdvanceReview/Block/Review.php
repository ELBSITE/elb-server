<?php
/**
 * @category Mageants AdvanceReview
 * @package Mageants_AdvanceReview
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvanceReview\Block;

use Mageants\AdvanceReview\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\SessionFactory;
use Magento\Review\Model\Rating;
use Magento\Review\Model\ReviewFactory;

/**
 * Class Review
 * @package Mageants\AdvanceReview\Block
 */
class Review extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mageants\AdvanceReview\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $_customerSessionFactory;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $_collection;

    /**
     * @var \Magento\Review\Model\Review
     */
    protected $_objectReview;

    /**
     * @var \Magento\Review\Model\Rating
     */
    protected $_ratingFactory;

    /**
     * Review constructor
     * @param \Mageants\AdvanceReview\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $collection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Element\Template\Context $context,
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory,
     * @param \Magento\Review\Model\Review $review,
     * @param \Magento\Review\Model\Rating $ratingFactory,
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory,
     * @param array $data
     */
    public function __construct(
        Data $helper,
        Session $customerSession,
        Registry $registry,
        Collection $collection,
        Context $context,
        SessionFactory $customerSessionFactory,
        \Magento\Review\Model\Review $review,
        Rating $ratingFactory,
        ReviewFactory $reviewFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_customerSessionFactory = $customerSessionFactory->create();
        $this->_helper = $helper;
        $this->_customerSession = $customerSession;
        $this->_registry = $registry;
        $this->_collection = $collection;
        $this->_objectReview = $review;
        $this->_storeManager = $context->getStoreManager();
        $this->_ratingFactory = $ratingFactory;
        $this->_reviewFactory = $reviewFactory;
        $this->redirect = $redirect;
    }
  
    /**
     * @return bool
     */
    public function getRefererUrl()
    {
        return $this->redirect->getRefererUrl();
    }

    /**
     * @return bool
     */
    public function isEnableAdvanceReview()
    {
        return $this->_helper->getConfig('advancereview/general/enable_frontend');
    }

    /**
     * @return bool
     */
    public function getLoggedinCustomerId()
    {
        if ($this->_customerSessionFactory->isLoggedIn()) {
            return $this->_customerSessionFactory->getId();
        }
        return false;
    }
    
    /**
     * @return bool
     */
    public function getAllowGroups()
    {
        $customergroupid = $this->_customerSession->getCustomer()->getGroupId();

        $customergroup = $this->_helper->getConfig('advancereview/general/mageants_multiselect');
        
        if ($customergroup!=null || !empty($customergroup)) {
            $customergrouparray= array();
            $customergrouparray=explode(",", $customergroup);
            
            if (in_array(0, $customergrouparray)) {
                if ($this->_customerSessionFactory->isLoggedIn()) {
                    $customergroupid = $this->_customerSession->getCustomer()->getGroupId();

                    if (in_array($customergroupid, $customergrouparray)) {
                        return true;
                    }
                } else {
                    return true;
                }
            } else {
                if ($this->_customerSessionFactory->isLoggedIn()) {
                    $customergroupid = $this->_customerSession->getCustomer()->getGroupId();
                    
                    if (in_array($customergroupid, $customergrouparray)) {
                        return true;
                    }
                }
            }
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function getOnlyPurchaser()
    {
        $onlyPurchaseReview = $this->_helper->getConfig('advancereview/general/Only_purchase_Review');

        if ($onlyPurchaseReview) {
            $customergroupid = $this->_customerSession->getCustomer()->getGroupId();  // get Customer Group Id
            $customergroup = $this->_helper->getConfig('advancereview/general/mageants_multiselect');
            
            if ($customergroup!=null || !empty($customergroup)) {
                $customergrouparray= array();
                $customergrouparray=explode(",", $customergroup);
                //var_dump($customergrouparray);exit();
                
                if (in_array($customergroupid, $customergrouparray)) {
                    $product = $this->_registry->registry('current_product');//get current product
                    $productId = $product->getId();

                    if ($this->_customerSessionFactory->isLoggedIn()) {
                        $customerId = $this->_customerSessionFactory->getId();
                    
                        $orders = $this->_collection->addFieldToFilter('customer_id', $customerId);

                        $products = array();
                        foreach ($orders as $order) {
                            foreach ($order->getAllVisibleItems() as $item) {
                                $products[] = $item->getProductId();
                            }
                        }
                        $product_list = array_unique($products);
                        
                        if (in_array($productId, $product_list)) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        if ($customergroupid == 0) {
                            if (in_array($customergroupid, $customergrouparray)) {
                                return true;
                            } else {
                                return false;
                            }
                        } else {
                            return false;
                        }
                    }
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * Return each Rating count value
     *
     * @return array
     */
    public function getAllStar()
    {
        $pid= $this->getProductId();

        $review = $this->_objectReview->getCollection()
                ->addEntityFilter('product', $pid)
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
                ->addFieldToSelect('review_id');
        $review->getSelect()->columns('detail.detail_id')->joinInner(
            ['vote' => $review->getTable('rating_option_vote')],
            'main_table.review_id = vote.review_id',
            array('review_value' => 'vote.value')
        );
        $review->getSelect()->order('review_value DESC');
        $review->getSelect()->columns('count(vote.vote_id) as total_vote')->group('detail.detail_id');
        $review->addRateVotes();
        
        for ($i = 5; $i >= 1; $i--) {
            $arrRatings[$i]['value'] = 0;
        }
        foreach ($review->getItems() as $_review) {
            foreach ($_review->getRatingVotes() as $_vote){
                $data = $_vote->getData();
                
                if ($data['rating_code'] == "Quality" || $data['rating_code'] == "Value" || $data['rating_code'] == "Price") {
                    $arrRatings[$data['value']]['value'] += 1;
                }
            }
        }
        
        return $arrRatings;
    }


    /**
     * Return current product id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->_registry->registry('current_product')->getId();
    }

    /**
     * Return average of Rating
     *
     * @return int|double
     */
    public function getAvgStar()
    {
        $rating=$this->getAllStar();
        $star=5;
        $allstar=$this->getReviewCount();
        $avgstar=0;
        $sumstar="";
        foreach ($rating as $ratings) {
            $sumstar = $star*$ratings['value'];
            $avgstar=$avgstar+$sumstar;
            $star--;
        }
        if($avgstar != '0' && $allstar != '0'){
            $avgstar=$avgstar/$allstar;
        }
        return sprintf('%0.2f', ($avgstar)/3);
    }

    /**
     * Return width of Rating Progressbar
     *
     * @return array
     */
    public function getRatingbarWidth()
    {
        $rating=$this->getAllStar();
        $width= [];
        
        $total=$this->getReviewCount();

        for ($i=5; $i>=1; $i--) {
            if($rating[$i]['value']!=0){
                $starwidth=($rating[$i]['value']*100/$total)/3;
                $width[$i]= sprintf('%0.2f', $starwidth);
            }
            else
            {
                $width[$i]='0.00';
            }
        }
        return $width;
    }

    /**
     * Return width of average Rating
     *
     * @return int|double
     */
    public function getAvgWidth()
    {
        $totalStar = 5;
        $avgStar = $this->getAvgStar();

        $avgWidth=$avgStar*100/$totalStar;

        return sprintf('%0.2f', $avgWidth);
    }

    /**
     * @return bool
     */
    public function getVerifiedPurchase()
    {
        $customergroupid = $this->_customerSession->getCustomer()->getGroupId();
            
        $product = $this->_registry->registry('current_product');
        $productId = $product->getId();

        if ($this->_customerSessionFactory->isLoggedIn()) {
            $customerId = $this->_customerSessionFactory->getId();
            
            $orders = $this->_collection->addFieldToFilter('customer_id', $customerId);

            $products = array();
            foreach ($orders as $order) {
                foreach ($order->getAllVisibleItems() as $item) {
                    $products[] = $item->getProductId();
                }
            }
            $product_list = array_unique($products);
                
            if (in_array($productId, $product_list)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Return url of media
     *
     * @return string
     */
    public function geMediaUrl($filename)
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl."AdvanceReview/".$filename;
    }

    /**
     * Return total review
     *
     * @return int|string
     */
    public function getReviewCount()
    {
        $product_id = $this->getProductId();
        $_ratingSummary = $this->_ratingFactory->getEntitySummary($product_id);
        $ratingCollection = $this->_reviewFactory->create()->getResourceCollection()->addStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)->addEntityFilter('product', $product_id);
        $review_count = count($ratingCollection);

        return $review_count;
    }

    /**
     * @return bool
     */
    public function getCheckLogin()
    {
        if ($this->_customerSessionFactory->isLoggedIn()) {
            return $this->_customerSessionFactory->getCustomer()->getId();
        }
        return false;
    }

    /**
     * Return total like
     *
     * @return int|string
     */
    public function getLikeCount($reviewId)
    {
        return $this->_helper->getLikeCount($reviewId);
    }

    /**
     * Return total dislike
     *
     * @return int|string
     */
    public function getDislikeCount($reviewId)
    {
        return $this->_helper->getDislikeCount($reviewId);
    }
}
