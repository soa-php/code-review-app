<?php

declare(strict_types=1);

namespace Payment\Infrastructure\Persistence\MongoDb;

use function Martinezdelariva\Hydrator\hydrate;
use MongoDB\Collection;
use Soa\EventSourcing\Repository\AggregateRoot;
use Soa\EventSourcing\Repository\Repository;

class RepositoryMongoDb implements Repository
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var string
     */
    private $stateFqcn;

    public function __construct(Collection $collection, string $stateFqcn)
    {
        $this->collection = $collection;
        $this->stateFqcn  = $stateFqcn;
    }

    public function findOfId(string $id): ?AggregateRoot
    {
        $result = $this->collection->findOne(['id' => $id]);

        return empty($result) ? null : hydrate($this->stateFqcn, $result);
    }
}
