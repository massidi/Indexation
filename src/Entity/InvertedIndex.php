<?php

namespace App\Entity;

use App\Repository\InvertedIndexRepository;
use Doctrine\ORM\Mapping as ORM;

//#[ORM\Entity]
#[ORM\Entity(repositoryClass: InvertedIndexRepository::class)]

class InvertedIndex
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(targetEntity: Word::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Word $word;

    #[ORM\ManyToOne(targetEntity: Document::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Document $document;

    #[ORM\Column(nullable: true)]
    private ?int $wordCount = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getWord(): Word
    {
        return $this->word;
    }

    public function setWord(Word $word): self
    {
        $this->word = $word;
        return $this;
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function setDocument(Document $document): self
    {
        $this->document = $document;
        return $this;
    }

    public function getWordCount(): ?int
    {
        return $this->wordCount;
    }

    public function setWordCount(?int $wordCount): static
    {
        $this->wordCount = $wordCount;

        return $this;
    }
}
