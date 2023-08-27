<?php

namespace App\Entity;

use App\Repository\ContributorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: ContributorRepository::class)]
class Contributor implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'contributors', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Collection $collection = null;

    #[ORM\Column(length: 255)]
    private ?string $user_name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $amount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCollection(): ?Collection
    {
        return $this->collection;
    }

    public function setCollection(?Collection $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    public function setUserName(string $user_name): static
    {
        $this->user_name = $user_name;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'user_name' => $this->user_name,
            'amount' => $this->amount,
        ];
    } 
}
