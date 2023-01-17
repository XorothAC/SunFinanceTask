<?php

namespace App\Entity;

use App\Repository\LoanRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LoanRepository::class)
 */
class Loan
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="uuid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="uuid")
     */
    private $customerId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $state;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $amount_issued;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $amount_to_pay;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function setCustomerId($customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getAmountIssued(): ?string
    {
        return $this->amount_issued;
    }

    public function setAmountIssued(string $amount_issued): self
    {
        $this->amount_issued = $amount_issued;

        return $this;
    }

    public function getAmountToPay(): ?string
    {
        return $this->amount_to_pay;
    }

    public function setAmountToPay(string $amount_to_pay): self
    {
        $this->amount_to_pay = $amount_to_pay;

        return $this;
    }
}
