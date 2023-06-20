<?php


namespace Mageants\AdvanceReview\Block\Adminhtml\Add;


class Addmedia extends \Magento\Backend\Block\Template
{
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Review\Model\ReviewFactory $reviewMediaFactory
    )
    {
        $this->_reviewMediaFactory = $reviewMediaFactory;
        $this->setTemplate("addmedia.phtml");
        parent::__construct($context);
    }

    
    /*public function getMediaCollection()
    {
        $reviewid = $this->getRequest()->getParam('id');
        $thisReviewMediaCollection = $this->_reviewMediaFactory->create();
        $thisReviewMediaCollection->load($reviewid);
        return $thisReviewMediaCollection;
    }*/

    /**
     * function
     * get review_images directory path
     *
     * @return string
     */
    public function getReviewMediaUrl()
    {
        $reviewMediaDirectoryPath = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'AdvanceReview/';
                

        return $reviewMediaDirectoryPath;
    }

}