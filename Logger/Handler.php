<?php

namespace Kudja\Webp\Logger;

use Magento\Framework\Logger\Handler\Base as BaseHandler;

class Handler extends BaseHandler
{

    protected $fileName = '/var/log/kudja_webp.log';

}
