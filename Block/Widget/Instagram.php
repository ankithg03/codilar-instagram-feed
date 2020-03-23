<?php

namespace Codilar\InstagramFeed\Block\Widget;

use Codilar\InstagramFeed\Model\InstagramDetails;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Codilar\InstagramFeed\Helper\Data;

class Instagram extends Template implements
    BlockInterface
{
    const INSTAGRAM_MODULE_STATUS_CONFIG = 'customer_instagram_feed/default/module_status';
    /**
     * @var Curl
     */
    private $curl;
    /**
     * @var InstagramDetails
     */
    private $instagramDetails;
    /**
     * @var Data
     */
    private $helperData;

    /**
     * Instagram constructor.
     * @param Context $context
     * @param Curl $curl
     * @param InstagramDetails $instagramDetails
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Curl $curl,
        InstagramDetails $instagramDetails,
        Data $helperData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setTemplate('widget/instagram.phtml');
        $this->curl = $curl;
        $this->instagramDetails = $instagramDetails;
        $this->helperData = $helperData;
    }

    /**
     * @param $userIdFromWidget
     * @return bool
     */
    public function getInstagramUserData($userIdFromWidget)
    {
        $status = $this->instagramDetails->initialize($userIdFromWidget);
        return $status;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->instagramDetails->getUserName();
    }

    /**
     * @return string
     */
    public function getProfilePictureSrc()
    {
        return $this->instagramDetails->getProfilePictureSrc();
    }

    /**
     * @return array
     */
    public function getPosts()
    {
        return $this->instagramDetails->getPosts();
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->instagramDetails->getUserId();
    }
    /**
     * @return string
     */
    public function getInstagramUrl(){
        return $this->instagramDetails->prepareInstagramUrl();
    }
    /**
     * return bool
     */
    public function isModuleEnable()
    {
        return (bool)$this->helperData->getConfig(self::INSTAGRAM_MODULE_STATUS_CONFIG);
    }
}
