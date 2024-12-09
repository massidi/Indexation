<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Word
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $term;

    #[ORM\Column]
    private ?int $wCounts = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTerm(): string
    {
        return $this->term;
    }

    public function setTerm(string $term): self
    {
        $this->term = $term;
        return $this;
    }

    public function getWCounts(): ?int
    {
        return $this->wCounts;
    }

    public function setWCounts(int $wCounts): static
    {
        $this->wCounts = $wCounts;

        return $this;
    }
}
