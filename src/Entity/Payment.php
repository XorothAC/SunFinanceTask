<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 */
class Payment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
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
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nationalSecurityNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $paymentResponse;

    public function getId(): ?int
    {
        return $this->id;
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

    public function setNationalSecurityNumber(string $nationalSecurityNumber): self
    {
        $this->nationalSecurityNumber = $nationalSecurityNumber;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPaymentResponse(): ?string
    {
        return $this->paymentResponse;
    }

    public function setPaymentResponse(string $paymentResponse): self
    {
        $this->paymentResponse = $paymentResponse;

        return $this;
    }
}
