<?php

namespace Forevue\HarvesterMetadataSdk\DataObjects;

use Forevue\HarvesterMetadataSdk\Client;
use Forevue\HarvesterMetadataSdk\DataObjects\Concerns\Resource;

class Source
{
    use Resource;

    public function __construct(
        protected readonly Client $client,
        protected readonly int $id,
        protected readonly string $name,
        protected readonly string $description,
        protected readonly string $urn,
        protected readonly int $provider_id,

        protected readonly int $min_crawl_interval,
        protected readonly int $max_crawl_interval,
        protected readonly int $min_recrawl_interval,
        protected readonly int $max_recrawl_interval,

        protected readonly string $created_at
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

    public function provider(): Provider
    {
        /** @var Provider */
        return $this->client->providers()->find($this->provider_id);
    }

    /** @return array{int, int} */
    public function crawlInterval(): array
    {
        return [$this->min_crawl_interval, $this->max_crawl_interval];
    }

    /** @return array{int, int} */
    public function recrawlInterval(): array
    {
        return [$this->min_recrawl_interval, $this->max_recrawl_interval];
    }

    /** @return Source[] */
    public function subSources(): array
    {
        $objects = $this->client->pdo->query(
            "SELECT sub_source_id FROM sub_sources WHERE parent_source_id = {$this->id}"
        );

        if (! $objects) {
            return [];
        }

        $objects = $objects->fetchAll();

        /** @var Source[] */
        return ! $objects ? [] : array_map(
            fn ($object) => $this->client->sources()->find($object['sub_source_id']),
            $objects

        );
    }
}
