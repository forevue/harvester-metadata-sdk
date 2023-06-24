<?php

namespace Forevue\HarvesterMetadataSdk\DataObjects\Concerns;

use ReflectionClass;

trait Resource
{
    /** @var string[] */
    protected static array $properties = [];

    public function id(): int
    {
        return $this->id;
    }

    public function urn(): string
    {
        return $this->urn;
    }

    public function type(): string
    {
        return explode(':', $this->urn)[3];
    }

    public function createdAt(): string
    {
        return $this->created_at;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        if (! static::$properties) {
            $reflection = new ReflectionClass($this);
            static::$properties = array_map(fn ($property) => $property->getName(), $reflection->getProperties());
        }

        $data = [
            'code_hash' => $this->codeHash(),
            'static_hash' => $this->staticHash(),
            'is_dirty' => $this->isDirty(),
            'type' => $this->type(),
        ];

        foreach (static::$properties as $property) {
            if ($property === 'client' || $property === 'properties') {
                continue;
            }

            $data[$property] = $this->$property;
        }

        return $data;
    }

    public function codeHash(): string
    {
        return explode(':', $this->urn)[4];
    }

    public function staticHash(): string
    {
        return explode(':', $this->urn)[5];
    }

    public function isDirty(): bool
    {
        return str_ends_with($this->codeHash(), '-dirty');
    }

    public function __toString(): string
    {
        return $this->urn;
    }
}
