<?php

namespace Felix\HarvesterMetadataSdk\Repositories;

use Felix\HarvesterMetadataSdk\Client;
use Felix\HarvesterMetadataSdk\DataObjects\Provider;
use Felix\HarvesterMetadataSdk\DataObjects\Source;

class SourceRepository
{
    public function __construct(
        protected readonly Client $client
    ) {
    }

    /** @return Provider[] */
    public function all(): array
    {
        $statement = $this->client->pdo->prepare('SELECT * FROM sources');
        $statement->execute();
        $objects = $statement->fetchAll();

        /** @var Provider[] */
        return ! $objects ? [] : array_map(fn ($object) => static::hydrate($this->client, $object), $objects);

    }

    public static function hydrate(Client $client, array|false $data): ?Source
    {
        return ! $data ? null : new Source($client, ...$data);

    }

    public function find(int $id): ?Source
    {
        $statement = $this->client->pdo->prepare('SELECT * FROM sources WHERE id = :id');
        $statement->execute(['id' => $id]);
        /** @var array<string, scalar>|false $object */
        $object = $statement->fetch();

        return static::hydrate($this->client, $object);
    }

    public function findByUrn(string $urn): ?Source
    {
        $statement = $this->client->pdo->prepare('SELECT * FROM sources WHERE urn = :urn');
        $statement->execute(['urn' => $urn]);
        /** @var array<string, scalar>|false $object */
        $object = $statement->fetch();

        return static::hydrate($this->client, $object);
    }
}
