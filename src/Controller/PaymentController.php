<?php

namespace App\Controller;

use \DateTime;
use App\Entity\Loan;
use App\Entity\Payment;
use App\Entity\PaymentOrder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class PaymentController extends AbstractController
{
    /**
     * @Route("/api/payment", name="payment")
     */
    public function payment(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger): JsonResponse
    {
        $data = $request->toArray()[0];
        $logger->info('Started payment process.');

        if (!isset($data['firstname'], $data['lastname'], $data['paymentDate'], $data['amount'], $data['description'], $data['refId'])) {
            return $this->json(['error' => 'Not all arguments set.']);
        }

        if ($this->validateDate($data['paymentDate'])) {
            return $this->json(['error' => 'Invalid date.']);
        }

        if ($data['amount'] < 0) {
            return $this->json(['error' => 'Invalid amount.']);
        }

        $pattern = '/^([LN])+\d{8}$/';
        if (!preg_match_all($pattern, $data['description'])) {
            return $this->json(['error' => 'Bad description.']);
        }

        try {
            $payment = $doctrine->getRepository(Payment::class)->find($data['refId']);
            if ($payment) {
                return $this->json(['error' => 'Duplicate entry.']);
            }
        } catch (Exception $ex) {
            return $this->json(['error' => $ex->getMessage()]);
        }

        $this->assignPayment($data, $doctrine, $logger);

        return $this->json(['message' => 'Payment successful.']);
    }

    public function assignPayment($data, ManagerRegistry $doctrine, LoggerInterface $logger) 
    {
        $loan = $doctrine->getRepository(Loan::class)->findOneBy(['reference' => $data['description']]);
        $paidAmount = $data['amount'];
        $amountToPay = $loan->getAmountToPay();

        if ($paidAmount == $amountToPay) {
            $this->createPayment($data, Payment::ASSIGNED, $loan, $doctrine);
            $this->updateLoan($paidAmount, $loan, Loan::PAID, $doctrine);
            $logger->info('Added new payment: ' . $data['description'] . '.');
        } else if ($paidAmount > $amountToPay) {
            $this->queuePaymentOrder($data, bcsub($paidAmount, $amountToPay), PaymentOrder::IN_PROGRESS, $doctrine);
            $this->createPayment($data, Payment::PARTIALLY_ASSIGNED, $loan, $doctrine);
            $this->updateLoan($amountToPay, $loan, Loan::PAID, $doctrine);
            $logger->info('Added new payment: ' . $data['description'] . '. Updated loans. Added new payment order.');
        } else {
            $this->createPayment($data, Payment::ASSIGNED, $loan, $doctrine);            
            $this->updateLoan($paidAmount, $loan, Loan::ACTIVE, $doctrine);
            $logger->info('Added new payment: ' . $data['description'] . '. Updated loans.');
        }

        $doctrine->getManager()->flush();
    }

    private function createPayment($data, $state, $loan, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $payment = new Payment();
        $payment->setId($data['refId']);
        $payment->setAmount($data['amount']);
        $payment->setDescription($loan);
        $payment->setNationalSecurityNumber($data['nsn'] ?? null);
        $payment->setPayerName($data['firstname']);
        $payment->setPayerSurname($data['lastname']);
        $payment->setPaymentDate(new DateTime($data['paymentDate']));
        $payment->setPaymentReference($data['paymentReference'] ?? null);
        $payment->setState($state);

        $entityManager->persist($payment);
    }
    
    private function updateLoan($paidAmount, Loan $loan, $state, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $loan->setAmountIssued($loan->getAmountIssued() + $paidAmount);
        $loan->setAmountToPay($loan->getAmountToPay() - $paidAmount);
        $loan->setState($state);
        
        $entityManager->persist($loan);       
    }
    
    private function queuePaymentOrder ($data, $refundAmount, $state, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $paymentOrder = new PaymentOrder();
        $paymentOrder->setAmountToRefund($refundAmount);
        $paymentOrder->setDescription($data['description']);
        $paymentOrder->setPaymentDate(new DateTime());
        $paymentOrder->setRefId($data['refId']);
        $paymentOrder->setState($state);

        $entityManager->persist($paymentOrder);
    }

    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
