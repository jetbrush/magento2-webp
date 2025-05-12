<?php

namespace Kudja\Webp\Service;

use Kudja\Webp\Logger\Logger;

class Converter
{

    private string $baseDir;

    public function __construct(
        private Config $config,
        private Queue  $queueService,
        private Logger $logger
    ) {
        $this->baseDir = BP . DIRECTORY_SEPARATOR . 'pub';
    }

    /**
     * @return void
     */
    public function convertPending(): void
    {
        $limit = $this->config->getConversionLimit();
        $batch = $this->queueService->getPendingQueue($limit);

        if (empty($batch)) {
            return;
        }

        $commandTpl = $this->config->getConversionCommand();

        foreach ($batch as $item) {
            $path = $item['path'] ?? null;
            $hash = $item['hash'] ?? null;

            if (!$path || !$hash) {
                continue;
            }

            $sourcePath = $this->baseDir . $path;
            $targetPath = $sourcePath . '.webp';

            try {
                if (!is_readable($sourcePath)) {
                    throw new \RuntimeException('Source not found: ' . $path);
                }

                $command = str_replace(
                    ['{src}', '{target}'],
                    [escapeshellarg($sourcePath), escapeshellarg($targetPath)],
                    $commandTpl
                );
                $exitCode = null;
                exec($command, $output, $exitCode);

                if ($exitCode !== 0 || !file_exists($targetPath)) {
                    throw new \RuntimeException('Conversion failed, code: ' . $exitCode);
                }

                if (filesize($targetPath) >= filesize($sourcePath)) {
                    unlink($targetPath);
                    throw new \RuntimeException("Converted filesize > original: $path");
                }


                $this->queueService->markProcessed($hash);
            } catch (\Throwable $e) {
                $this->logger->warning('[Webp] ' . $e->getMessage());
                $this->queueService->markFailed($hash);
            }
        }
    }

}
