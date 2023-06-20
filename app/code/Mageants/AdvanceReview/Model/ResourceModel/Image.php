<?php
 /**
 * @category Mageants Product360Image
 * @package Mageants_Product360Image
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\AdvanceReview\Model\ResourceModel;

use \Magento\Framework\UrlInterface;
use \Magento\Framework\Filesystem;
use \Magento\Framework\View\Asset\Repository;
use \Magento\Store\Model\StoreManagerInterface;
		
class Image
{
	
	/**
     * @var _storeManager
     */
	protected $_storeManager;
    /**
     * Media sub folder
     * 
     * @var string
     */
    protected $_subDir = '/AdvanceReview';

    /**
     * URL builder
     * 
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * File system model
     * 
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var \Magento\Framework\View\Asset\Repositoryp
     */
    protected $_assetRepo;

    /**
     * constructor
     * 
     * @param UrlInterface $urlBuilder
     * @param Filesystem $fileSystem
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Filesystem $fileSystem,
		Repository $assetRepo,
		StoreManagerInterface $storeManager
    )
    {
        $this->_urlBuilder = $urlBuilder;
		
        $this->_fileSystem = $fileSystem;
		
		$this->_assetRepo = $assetRepo;
		
		$this->_storeManager = $storeManager;
    }

    /**
     * get images base url
     *
     * @return string
     */
    public function getBaseUrl($product_id)
    {
        return $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]).$this->_subDir.'/image/'.$this->getConvertedId($product_id);
    }
    
	/**
     * get base image dir
     *
     * @return string
     */
    public function getBaseDir($product_id = '') 
    {
        return $this->_fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath($this->_subDir.'/image/'.$this->getConvertedId($product_id));
    }
	
    /**
     * get product 360 images base url
     *
     * @return string
     */
    public function getProduct360Url($product_id, $image_name)
    {
        return $this->getBaseUrl($product_id) . '/'. $image_name;
    }
    /**
     * get product 360 path from media
     *
     * @return string
     */
    public function getProduct360Path($product_id, $image_name)
    {
        return $this->_subDir.'/image/'.$this->getConvertedId($product_id) . '/'. $image_name;
    }
	
	/**
     * get convert product to base64_encode
     *
     * @return string
     */
    public function getConvertedId($product_id = null) 
    {
		if($product_id)
		{
			return base64_encode($product_id);
		}
		else
		{
			return '';
		}
    }
    
    
		

}
