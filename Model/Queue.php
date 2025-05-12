<?php

namespace Kudja\Webp\Model;

use Magento\Framework\Model\AbstractModel;

class Queue extends AbstractModel
{

    public const STATUS_PENDING = 0;
    public const STATUS_ERROR   = -1;
    public const STATUS_SUCCESS = 1;

    protected function _construct(): void
    {
        $this->_init(ResourceModel\Queue::class);
    }

}
