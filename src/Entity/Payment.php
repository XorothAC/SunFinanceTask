<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 */
class Payment
{
    const ASSIGNED = 'ASSIGNED';
    const PARTIALLY_ASSIGNED = 'PARTIALLY_ASSIGNED';

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $paymentDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $payerName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $payerSurname;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nationalSecurityNumber;

    /**
     * @ORM\ManyToOne(targetEntity=Loan::class, inversedBy="reference")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $paymentReference;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $state;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTimeInterface $paymentDate): self
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getPayerName(): ?string
    {
        return $this->payerName;
    }

    public function setPayerName(string $payerName): self
    {
        $this->payerName = $payerName;

        return $this;
    }

    public function getPayerSurname(): ?string
    {
        return $this->payerSurname;
    }

    public function setPayerSurname(string $payerSurname): self
    {
        $this->payerSurname = $payerSurname;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getNationalSecurityNumber(): ?string
    {
        return $this->nationalSecurityNumber;
    }

    public function setNationalSecurityNumber(?string $nationalSecurityNumber): self
    {
        $this->nationalSecurityNumber = $nationalSecurityNumber;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(Loan $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPaymentReference(): ?string
    {
        return $this->paymentReference;
    }

    public function setPaymentReference(?string $paymentReference): self
    {
        $this->paymentReference = $paymentReference;

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
}
