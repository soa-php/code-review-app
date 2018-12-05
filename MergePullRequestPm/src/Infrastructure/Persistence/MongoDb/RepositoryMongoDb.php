<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Persistence\MongoDb;

use function Martinezdelariva\Hydrator\hydrate;
use function Martinezdelariva\Hydrator\extract;
use MongoDB\Collection;
use Soa\IdentifierGenerator\IdentifierGenerator;
use Soa\ProcessManager\Domain\Process;
use Soa\ProcessManager\Domain\State;
use Soa\ProcessManager\Infrastructure\Persistence\Repository;

class RepositoryMongoDb implements Repository
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var string
     */
    private $processFqcn;

    /**
     * @var IdentifierGenerator
     */
    private $identifierGenerator;

    /**
     * @var string
     */
    private $stateFqcn;

    public function __construct(Collection $collection, string $processFqcn, string $stateFqcn, IdentifierGenerator $identifierGenerator)
    {
        $this->collection          = $collection;
        $this->processFqcn         = $processFqcn;
        $this->identifierGenerator = $identifierGenerator;
        $this->stateFqcn           = $stateFqcn;
    }

    public function findOfId(string $id): Process
    {
        $result = $this->collection->findOne(['id' => $id]);

        if (empty($result)) {
            return hydrate($this->processFqcn, ['id' => $this->identifierGenerator->nextIdentity(), 'state' => State::INITIALIZED()]);
        }

        $state           = $result['state'];
        $result['state'] = $this->stateFqcn::$state();

        return hydrate($this->processFqcn, $result);
    }

    public function save(Process $process): void
    {
        $data          = extract($process);
        $data['state'] = $process->currentState()->getName();

        $this->collection->replaceOne(['_id' => $process->id()], $data, ['upsert' => true]);
    }
}
