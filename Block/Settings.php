<?php

namespace Kudja\Webp\Block;

use Kudja\Webp\Service\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Settings extends Template
{
    public function __construct(
        private Config $config,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }

    /**
     * @return array
     */
    public function getAllowedTags(): array
    {
        return $this->config->getAllowedTags();
    }

    /**
     * @return array
     */
    public function getAllowedAttributes(): array
    {
        return $this->config->getAllowedAttributes();
    }

}
