<?php

namespace Codilar\InstagramFeed\Model;

use Codilar\InstagramFeed\Logger\Logger;
use Exception;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;

class InstagramDetails
{
    const INSTAGRAM_BASE_URL = 'https://www.instagram.com/';

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var $userData
     */
    private $userData;

    /**
     * @var $userName
     */
    private $userName;

    /**
     * @var $posts
     */
    private $posts;

    /**
     * @var $accountLink
     */
    private $accountLink;

    /**
     * @var $profilePicture
     */
    private $profilePicture;

    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * $var $userIdFromWidget
     */
    private $userIdFromWidget;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * InstagramDetails constructor.
     * @param Curl $curl
     * @param Json $json
     * @param CurrentCustomer $currentCustomer
     * @param Logger $logger
     */
    public function __construct(
        Curl $curl,
        Json $json,
        CurrentCustomer $currentCustomer,
        Logger $logger
    ) {
        $this->curl = $curl;
        $this->json = $json;
        $this->currentCustomer = $currentCustomer;
        $this->logger = $logger;
    }

    /**
     * @param $userIdFromWidget
     * @return bool
     */
    public function initialize($userIdFromWidget)
    {
        try {
            $this->userIdFromWidget = $userIdFromWidget;
            if ($this->getUserId()) {
                $userGet = $this->getApiData();
                if ($userGet) {
                    $edges = $userGet["edge_owner_to_timeline_media"]["edges"];
                    $posts = [];
                    foreach ($edges as $edge) {
                        $post["main_picture"] = $edge["node"]["display_url"];
                        $post["thumbnail_src"] = $edge["node"]["thumbnail_src"];
                        if (isset($edge["node"]["edge_media_to_caption"]["edges"][0]["node"]["text"])) {
                            $post["post_description"] =
                                $edge["node"]["edge_media_to_caption"]["edges"][0]["node"]["text"];
                        }
                        $post["comment"] = $edge["node"]["edge_media_to_comment"]["count"];
                        $post["likes"] = $edge["node"]["edge_liked_by"]["count"];
                        $post["post_url"] = self::INSTAGRAM_BASE_URL . "p/" . $edge["node"]["shortcode"];
                        $posts[]=$post;
                    }
                    $this->setUserInformation(
                        $userGet["full_name"],
                        $userGet["profile_pic_url_hd"],
                        self::INSTAGRAM_BASE_URL . $this->getUserId() . "/",
                        $posts
                    );
                    return true;
                }
            }
        } catch (Exception $exception) {
            $this->logger->addWarning($exception->getMessage());
        }
        return false;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        try {
            if ($this->userIdFromWidget) {
                $userName = trim(explode('instagram.com/', $this->userIdFromWidget)[1], "/");
                return $userName;
            } elseif ($this->currentCustomer->getCustomerId()) {
                if ($this->currentCustomer->getCustomer()->getCustomAttribute("instagram_link")!=null) {
                    $userName = $this->currentCustomer->getCustomer()->getCustomAttribute("instagram_link")->getValue();
                    if (!empty($userName)) {
                        $userName = trim(explode('instagram.com/', $userName)[1], "/");
                        return $userName;
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->logger->addWarning($exception->getMessage());
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getApiData()
    {
        try {
            $curl = $this->curl;
            $url = self::INSTAGRAM_BASE_URL . $this->getUserId() . '/?__a=1';
            $curl->get($url);
            $response = $curl->getBody();
            $this->userData = $this->json->unserialize($response)['graphql']['user'];
            return $this->userData;
        } catch (Exception $exception) {
            $this->logger->addWarning($exception->getMessage());
        }
        return false;
    }
    /**
     * @return string
     */
    public function prepareInstagramUrl()
    {
        return self::INSTAGRAM_BASE_URL . $this->getUserId();
    }
    /**
     * @param $userName
     * @param $profilePicture
     * @param $accountLink
     * @param $posts
     * @return array
     */
    public function setUserInformation($userName, $profilePicture, $accountLink, $posts)
    {
        try {
            $this->userName = $userName;
            $this->accountLink = $accountLink;
            $this->profilePicture = $profilePicture;
            $this->posts = $posts;
            return ["status"=>true, "message"=>"data is successfully set"];
        } catch (Exception $exception) {
            return ["status"=>false, "message"=>$exception->getMessage()];
        }
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getAccountLink()
    {
        return $this->accountLink;
    }

    /**
     * @return array
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @return mixed
     */
    public function getProfilePictureSrc()
    {
        return $this->profilePicture;
    }
}
