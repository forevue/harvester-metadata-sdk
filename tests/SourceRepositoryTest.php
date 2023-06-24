<?php

use Felix\HarvesterMetadataSdk\Client;
use Felix\HarvesterMetadataSdk\DataObjects\Source;

beforeEach(function () {
    $this->pdo = new PDO('sqlite::memory:', options: [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $this->client = new Client(pdo: $this->pdo);

    $dump = file_get_contents(__DIR__.'/fixtures/dump.sql');
    $this->pdo->exec($dump);
});

it('can retrieve all sources', function () {
    $sources = $this->client->sources()->all();

    expect($sources)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Source::class);

    expect($sources)->sequence(
        fn ($source) => $source->id()->toBe(1)->name()->toBe('EA Forum Posts'),
        fn ($source) => $source->id()->toBe(2)->name()->toBe('EA Forum Comments'),
        fn ($source) => $source->id()->toBe(3)->name()->toBe('EA Wiki'),
    );
});

it('can retrieve a source by id', function () {
    $source = $this->client->sources()->find(1);

    expect($source)
        ->id()->toBe(1)
        ->name()->toBe('EA Forum Posts')
        ->description()->toBe('Posts from the EA Forum')
        ->urn()->toBe('urn:forevue:source:eaposts:3484d3d:4ea41849cb1ca466e20ceebc5f83a576d33abbf103df53b2a0adaf2dc69166f4')
        ->crawlInterval()->toBe([24 * 60 * 60, 2 * 24 * 60 * 60])
        ->recrawlInterval()->toBe([30 * 24 * 60 * 60, 2 * 30 * 24 * 60 * 60])
        ->isDirty()->toBeFalse();
});

it('can retrieve a source by urn', function () {
    $source = $this->client->sources()->findByUrn('urn:forevue:source:eaposts:3484d3d:4ea41849cb1ca466e20ceebc5f83a576d33abbf103df53b2a0adaf2dc69166f4');

    expect($source)
        ->id()->toBe(1)
        ->name()->toBe('EA Forum Posts')
        ->urn()->toBe('urn:forevue:source:eaposts:3484d3d:4ea41849cb1ca466e20ceebc5f83a576d33abbf103df53b2a0adaf2dc69166f4');
});

it('returns null when a source is not found', function () {
    $source = $this->client->sources()->find(4);
    expect($source)->toBeNull();

    $source = $this->client->sources()->findByUrn('something');
    expect($source)->toBeNull();
});

it("can get the source's sub-sources", function () {
    $source = $this->client->sources()->find(1);
    $subSources = $source->subSources();

    expect($subSources)
        ->toHaveCount(1)
        ->each->toBeInstanceOf(Source::class);

    expect($subSources)->sequence(
        fn ($source) => $source->id()->toBe(2)->name()->toBe('EA Forum Comments'),
    );
});

it('can retrieve its provider', function () {
    $source = $this->client->sources()->find(1);
    $sourceProvider = $source->provider();

    $provider = $this->client->providers()->find(1);

    expect($sourceProvider)
        ->id()->toBe($provider->id())
        ->urn()->toBe($provider->urn());
});
