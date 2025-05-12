<?php

namespace Kudja\Webp\Cron;

use Kudja\Webp\Service\Config;
use Kudja\Webp\Service\Converter;
use Kudja\Webp\Service\Queue as QueueService;

class ProcessQueue
{

    public function __construct(
        private Config $config,
        private Converter $converter,
        private QueueService $queueService,
    ) {}

    /**
     * @return void
     */
    public function execute(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $this->converter->convertPending();
        $this->queueService->purgeProcessed();
    }

}
