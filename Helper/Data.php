<?php
namespace Codilar\InstagramFeed\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class Data extends AbstractHelper
{
    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @return string|null
     */
    public function getConfig($key)
    {
        $result = $this->scopeConfig->getValue(
            $key,
            ScopeInterface::SCOPE_STORE
        );
        return $result;
    }
}
