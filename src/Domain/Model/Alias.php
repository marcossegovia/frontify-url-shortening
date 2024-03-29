<?php

namespace App\Domain\Model;

class Alias implements \JsonSerializable
{
    private string $alias;

    private \DateTimeImmutable $createdAt;

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function equals(string $alias): bool
    {
        return $this->alias === $alias;
    }

    public function jsonSerialize(): array
    {
        return [
            'alias' => $this->alias,
            'createdAt' => $this->createdAt->format(\DateTimeImmutable::ATOM),
        ];
    }
}
