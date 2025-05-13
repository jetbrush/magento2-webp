<?php

namespace Kudja\Webp\Service;

use Kudja\Webp\Model\ResourceModel\Queue\Collection;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Kudja\Webp\Logger\Logger;

class Queue
{
    private AdapterInterface $connection;
    private string           $table;

    public function __construct(
        private Collection $queueCollection,
        private Logger     $logger,
        ResourceConnection $resource
    )
    {
        $this->connection = $resource->getConnection();
        $this->table      = $this->connection->getTableName('kudja_webp_queue');
    }

    /**
     * @param array $paths
     *
     * @return void
     */
    public function batchAddImages(array $paths): void
    {
        try {
            if (empty($paths)) {
                return;
            }

            $paths    = array_unique($paths);
            $hashes   = array_map('md5', $paths);

            $existing = $this->queueCollection->addFieldToFilter('hash', ['in' => $hashes])
                                              ->getColumnValues('path');

            $toInsert = array_diff($paths, $existing);
            if (empty($toInsert)) {
                return;
            }

            $rows = [];
            $bind = [];
            foreach ($paths as $index => $path) {
                $path = trim($path);
                if (!$path) {
                    continue;
                }

                $rows[] = "(:path{$index}, :hash{$index}, 0)";

                $bind["path{$index}"] = $path;
                $bind["hash{$index}"] = md5($path);
            }

            if (empty($rows)) {
                return;
            }

            $sql = sprintf(
                'INSERT IGNORE INTO %s (path, hash, status) VALUES %s',
                $this->table,
                implode(', ', $rows)
            );

            $this->connection->query($sql, $bind);
        } catch (\Exception $e) {
            $this->logger->error('Error adding images to queue: ' . $e->getMessage());
            return;
        }
    }

    /**
     * @param int      $limit
     *
     * @return array
     */
    public function getPendingQueue(int $limit): array
    {
        $select = $this->connection->select()
                                   ->from($this->table)
                                   ->where('status = ?', \Kudja\Webp\Model\Queue::STATUS_PENDING)
                                   ->limit($limit);

        return $this->connection->fetchAll($select);
    }

    /**
     * @param string $hash
     *
     * @return void
     */
    public function markProcessed(string $hash): void
    {
        $this->connection->update(
            $this->table,
            ['status' => \Kudja\Webp\Model\Queue::STATUS_SUCCESS],
            ['hash = ?' => $hash]
        );
    }

    /**
     * @param string $hash
     *
     * @return void
     */
    public function markFailed(string $hash): void
    {
        $this->connection->update(
            $this->table,
            ['status' => \Kudja\Webp\Model\Queue::STATUS_ERROR],
            ['hash = ?' => $hash]
        );
    }

    /**
     * @return void
     */
    public function purgeProcessed(): void
    {
        $this->connection->delete(
            $this->table,
            ['status = ?' => \Kudja\Webp\Model\Queue::STATUS_SUCCESS]
        );
    }

}
