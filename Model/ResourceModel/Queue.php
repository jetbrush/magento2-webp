<?php

namespace Kudja\Webp\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Queue extends AbstractDb
{

    protected function _construct(): void
    {
        $this->_init('kudja_webp_queue', 'queue_id');
    }

}
