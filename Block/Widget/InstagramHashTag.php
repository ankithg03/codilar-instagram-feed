<?php


namespace Codilar\InstagramFeed\Block\Widget;


use Codilar\InstagramFeed\Helper\Data;
use Codilar\InstagramFeed\Model\InstagramHashTag as InstaHashTagModel;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;

class InstagramHashTag extends Template implements
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
     * @param InstaHashTagModel $instagramDetails
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Curl $curl,
        InstaHashTagModel $instagramDetails,
        Data $helperData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setTemplate('widget/instagramhashtag.phtml');
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
     * @return array
     */
    public function getPosts()
    {
        return $this->instagramDetails->getPosts();
    }

    /**
     * @return string
     */
    public function getHashTag()
    {
        return $this->instagramDetails->getHashTag();
    }

    /**
     * return bool
     */
    public function isModuleEnable()
    {
        return (bool)$this->helperData->getConfig(self::INSTAGRAM_MODULE_STATUS_CONFIG);
    }
}