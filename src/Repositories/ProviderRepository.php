<?php

namespace Felix\HarvesterMetadataSdk\Repositories;

use Felix\HarvesterMetadataSdk\Client;
use Felix\HarvesterMetadataSdk\DataObjects\Provider;

class ProviderRepository
{
    public function __construct(protected readonly Client $client)
    {
    }

    /** @return Provider[] */
    public function all(): array
    {
        $statement = $this->client->pdo->prepare('SELECT * FROM providers');
        $statement->execute();
        $objects = $statement->fetchAll();

        if (! $objects) {
            return [];
        }

        /** @var Provider[] */
        return array_map(
            fn ($object) => static::hydrate($this->client, $object),
            $objects
        );
    }

    public static function hydrate(Client $client, array|false $data): ?Provider
    {
        return ! $data ? null : new Provider($client, ...$data);

    }

    public function find(int $id): ?Provider
    {
        $statement = $this->client->pdo->prepare('SELECT * FROM providers WHERE id = :id');
        $statement->execute(['id' => $id]);
        /** @var array<string, mixed>|false $object */
        $object = $statement->fetch();

        return static::hydrate($this->client, $object);
    }

    public function findByUrn(string $urn): ?Provider
    {
        $statement = $this->client->pdo->prepare('SELECT * FROM providers WHERE urn = :urn');
        $statement->execute(['urn' => $urn]);
        /** @var array<string, mixed>|false $object */
        $object = $statement->fetch();

        return static::hydrate($this->client, $object);
    }
}
