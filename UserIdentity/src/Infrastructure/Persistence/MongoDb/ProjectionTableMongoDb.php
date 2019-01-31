<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Persistence\MongoDb;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;
use Soa\EventSourcing\Projection\ProjectionTable;

class ProjectionTableMongoDb implements ProjectionTable
{
    /**
     * @var Collection
     */
    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function findOfId(string $id): array
    {
        $result = $this->collection->findOne(['id' => $id]);

        return empty($result) ? [] : $result;
    }

    public function save(string $id, array $projection): void
    {
        $projection = $this->setTime($projection);

        $this->collection->replaceOne(['id' => $id], $projection, ['upsert' => true]);
    }

    private function setTime(array $projection): array
    {
        $now = new UTCDateTime();

        if (!isset($projection['createdAt'])) {
            $projection['createdAt'] = $now;
        }

        $projection['updatedAt'] = $now;

        return $projection;
    }
}
