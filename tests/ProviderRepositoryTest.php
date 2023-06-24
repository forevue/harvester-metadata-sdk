<?php

use Forevue\HarvesterMetadataSdk\Client;
use Forevue\HarvesterMetadataSdk\DataObjects\Provider;
use Forevue\HarvesterMetadataSdk\DataObjects\Source;

beforeEach(function () {
    $this->client = new Client(dsn: 'sqlite::memory:');

    $this->client->pdo->exec(
        file_get_contents(__DIR__.'/fixtures/dump.sql')
    );
});

it('can retrieve all providers', function () {
    $providers = $this->client->providers()->all();

    expect($providers)->toHaveCount(1);
    expect($providers[0])
        ->toBeInstanceOf(Provider::class)
        ->id()->toBe(1)
        ->name()->toBe('EA Forum')
        ->description()->toBe('The Effective Altruism Forum is a place for discussion of effective altruism and related topics.')
        ->url()->toBe('https://forum.effectivealtruism.org/')
        ->urn()->toBe('urn:forevue:provider:eaforum:3484d3d:97fa66d1cc41bae08b5c88c7322b35b17cf5a1b2559205eb2aa3bd81b588d162')
        ->type()->toBe('eaforum')
        ->codeHash()->toBe('3484d3d')
        ->staticHash()->toBe('97fa66d1cc41bae08b5c88c7322b35b17cf5a1b2559205eb2aa3bd81b588d162')
        ->createdAt()->toBe('2023-06-24 12:36:56.109962')
        ->isDirty()->toBeFalse();
});

it('can retrieve a provider by id', function () {
    $provider = $this->client->providers()->find(1);

    expect($provider)
        ->id()->toBe(1)
        ->urn()->toBe('urn:forevue:provider:eaforum:3484d3d:97fa66d1cc41bae08b5c88c7322b35b17cf5a1b2559205eb2aa3bd81b588d162');
});

it('can retrieve a provider by urn', function () {
    $provider = $this->client->providers()->findByUrn('urn:forevue:provider:eaforum:3484d3d:97fa66d1cc41bae08b5c88c7322b35b17cf5a1b2559205eb2aa3bd81b588d162');

    expect($provider)
        ->id()->toBe(1)
        ->urn()->toBe('urn:forevue:provider:eaforum:3484d3d:97fa66d1cc41bae08b5c88c7322b35b17cf5a1b2559205eb2aa3bd81b588d162');
});

it('returns null when a provider is not found', function () {
    $provider = $this->client->providers()->find(2);
    expect($provider)->toBeNull();

    $provider = $this->client->providers()->findByUrn('something');
    expect($provider)->toBeNull();
});

it("can retrieve the providers' sources", function () {
    $provider = $this->client->providers()->find(1);

    $sources = $provider->sources();

    expect($sources)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Source::class);

    expect($sources)
        ->sequence(
            fn ($source) => $source->name()->toBe('EA Forum Posts'),
            fn ($source) => $source->name()->toBe('EA Forum Comments'),
            fn ($source) => $source->name()->toBe('EA Wiki'),
        );
});

it('returns an empty array if there are no providers', function () {
    $this->client->pdo->exec('DELETE FROM providers;');

    $providers = $this->client->providers()->all();

    expect($providers)->toBeArray()->toBeEmpty();
});

it('returns an empty array if the providers has no sources', function () {
    $this->client->pdo->exec('DELETE FROM sources;');

    $sources = $this->client->providers()->find(1)->sources();

    expect($sources)->toBeArray()->toBeEmpty();
});

// This tests aren't present in the SourceRepositoryTest because it tests the underlying Resource trait.
it('can be serialized to JSON', function () {
    $provider = $this->client->providers()->find(1);

    expect($provider->jsonSerialize())->toBe([
        'code_hash' => $provider->codeHash(),
        'static_hash' => $provider->staticHash(),
        'is_dirty' => $provider->isDirty(),
        'type' => $provider->type(),
        'id' => $provider->id(),
        'name' => $provider->name(),
        'description' => $provider->description(),
        'url' => $provider->url(),
        'urn' => $provider->urn(),
        'created_at' => $provider->createdAt(),
    ]);
});

it('can be converted to a string', function () {
    $provider = $this->client->providers()->find(1);

    expect((string) $provider)->toBe($provider->urn());
});
