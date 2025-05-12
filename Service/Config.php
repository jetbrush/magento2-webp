<?php

namespace Kudja\Webp\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{

    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private UrlInterface $urlBuilder
    ) {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return (bool)$this->scopeConfig->getValue('kudja_webp/general/enable', ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getAllowedTags(?int $storeId = null): array
    {
        $tags = $this->scopeConfig->getValue('kudja_webp/general/allowed_tags', ScopeInterface::SCOPE_STORE, $storeId);
        return array_map('trim', explode(',', $tags ?? ''));
    }

    public function getAllowedAttributes(?int $storeId = null): array
    {
        $attributes = $this->scopeConfig->getValue('kudja_webp/general/allowed_attributes', ScopeInterface::SCOPE_STORE, $storeId);
        return array_map('trim', explode(',', $attributes ?? ''));
    }

    public function getConversionCommand(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue('kudja_webp/general/conversion_command', ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getConversionLimit(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue('kudja_webp/general/conversion_limit', ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getBaseMediaUrl(?int $storeId = null): string
    {
        return rtrim($this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]), '/') . '/';
    }

    public function getBaseStaticUrl(?int $storeId = null): string
    {
        return rtrim($this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_STATIC]), '/') . '/';
    }

    public function getBaseUrl(?int $storeId = null): string
    {
        return str_replace('/media/', '', $this->getBaseMediaUrl($storeId)) . '/';
    }

}
