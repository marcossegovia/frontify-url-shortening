<?php

namespace App\Domain\Model;

use Ramsey\Uuid\UuidInterface;

class Url implements \JsonSerializable
{
    private UuidInterface $id;

    private string $url;

    private \DateTimeImmutable $createdAt;

    /**
     * @var Alias[]
     */
    private array $aliases;


    public function equals(UuidInterface $id): bool
    {
        return $id->equals($id);
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function setAliases(array $aliases): void
    {
        $this->aliases = $aliases;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'url' => $this->url,
            'createdAt' => $this->createdAt->format(\DateTimeImmutable::ATOM),
            'aliases' => $this->aliases
        ];
    }
}
