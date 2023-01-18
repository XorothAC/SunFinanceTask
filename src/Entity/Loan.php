<?php

namespace App\Entity;

use App\Repository\LoanRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=LoanRepository::class)
 */
class Loan
{
    const ACTIVE = 'ACTIVE';
    const PAID = 'PAID';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="uuid")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="id")
     */
    private $customer;

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
    
    /**
     * @ORM\OneToMany(targetEntity=Loan::class, mappedBy="description")
     */
    private $payments;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerId(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomerId(?Customer $customer): self
    {
        $this->customer = $customer;

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
    
    public function getPayments(): Collection
    {
        return $this->payments;
    }
}
