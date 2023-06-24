<?php

namespace Felix\HarvesterMetadataSdk\DataObjects;

use Felix\HarvesterMetadataSdk\Client;
use Felix\HarvesterMetadataSdk\DataObjects\Concerns\Resource;
use Felix\HarvesterMetadataSdk\Repositories\SourceRepository;
use JsonSerializable;

class Provider implements JsonSerializable
{
    use Resource;

    public function __construct(
        protected readonly Client $client,
        protected readonly int $id,
        protected readonly string $name,
        protected readonly string $description,
        protected readonly string $url,
        protected readonly string $urn,
        protected readonly string $created_at,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function url(): string
    {
        return $this->url;
    }

    /** @return Source[]c */
    public function sources(): array
    {
        $objects = $this->client->pdo->query(
            "SELECT * FROM sources WHERE provider_id = {$this->id}"
        );

        if (! $objects) {
            return [];
        }

        $objects = $objects->fetchAll();

        /** @var Source[] */
        return ! $objects ? [] : array_map(fn ($object) => SourceRepository::hydrate($this->client, $object), $objects);
    }
}
