<?php

namespace Forevue\HarvesterMetadataSdk;

use Forevue\HarvesterMetadataSdk\Repositories\ProviderRepository;
use Forevue\HarvesterMetadataSdk\Repositories\SourceRepository;
use PDO;

class Client
{
    public PDO $pdo;

    public function __construct(
        string $host = 'localhost',
        string $password = '',
        string $port = '5432',
        string $user = 'postgres',
        string $database = 'postgres',
        string $driver = 'pgsql',
        ?string $dsn = null,
        ?PDO $pdo = null
    ) {
        if ($pdo !== null) {
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo = $pdo;

            return;
        }

        if ($dsn === null) {
            $dsn = "{$driver}:host=$host;port=$port;dbname=$database;user=$user;password=$password";
        }

        $this->pdo = new PDO($dsn, options: [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

    }

    public function sources(): SourceRepository
    {
        return new SourceRepository($this);
    }

    public function providers(): ProviderRepository
    {
        return new ProviderRepository($this);
    }
}
