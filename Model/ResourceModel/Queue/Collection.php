<?php

namespace Kudja\Webp\Model\ResourceModel\Queue;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Kudja\Webp\Model\Queue as QueueModel;
use Kudja\Webp\Model\ResourceModel\Queue as QueueResource;

class Collection extends AbstractCollection
{

    protected function _construct(): void
    {
        $this->_init(QueueModel::class, QueueResource::class);
    }

}
