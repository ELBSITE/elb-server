<?php

namespace Mageants\AdvanceReview\Observer;

class AdminProductReviewSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \RLTSquare\ProductReviewImages\Model\ReviewMediaFactory
     */
    protected $_reviewMediaFactory;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_fileHandler;

    /**
     * AdminProductReviewSaveAfter constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Driver\File $fileHandler
     * @param \RLTSquare\ProductReviewImages\Model\ReviewMediaFactory $reviewMediaFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $fileHandler,
        \Magento\Review\Model\Review $reviewMediaFactory
    )
    {
        $this->_request = $request;
        $this->_fileHandler = $fileHandler;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_reviewMediaFactory = $reviewMediaFactory;
    }


    /**
     * function
     * executes after review is saved
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        echo die('test');
        $target = $this->_mediaDirectory->getAbsolutePath('review_images');
        $deletedMediaString = $this->_request->getParam('deleted_media');
        

        if ($deletedMediaString)
            try {
                    $id = $deletedMediaString;
                    $reviewMedia = $this->_reviewMediaFactory->create()->load($id);
                    $path = $target . $reviewMedia->getMediaUrl();
                    
                    if ($this->_fileHandler->isExists($path)) {

                            $this->_fileHandler->deleteFile($path);
                    }
                    $reviewMedia->delete();
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while updating review attachment(s).'));
            }
    }
}