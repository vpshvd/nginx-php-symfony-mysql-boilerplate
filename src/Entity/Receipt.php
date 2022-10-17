<?php

namespace App\Entity;

use App\Repository\ReceiptRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReceiptRepository::class)]
class Receipt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shortUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fullUrl = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $visual = null;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    private string $uuid;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     */
    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getShortUrl(): ?string
    {
        return $this->shortUrl;
    }

    public function setShortUrl(?string $shortUrl): self
    {
        $this->shortUrl = $shortUrl;

        return $this;
    }

    public function getFullUrl(): ?string
    {
        return $this->fullUrl;
    }

    public function setFullUrl(?string $fullUrl): self
    {
        $this->fullUrl = $fullUrl;

        return $this;
    }

    public function getVisual(): ?string
    {
        return $this->visual;
    }

    public function setVisual(?string $visual): self
    {
        $this->visual = $visual;

        return $this;
    }
}
