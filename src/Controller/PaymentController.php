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

class PaymentController extends AbstractController
{
    /**
     * @Route("/api/payment", name="payment")
     */
    public function payment(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $data = $request->toArray()[0];

        if (!isset($data['firstname'], $data['lastname'], $data['paymentDate'], $data['amount'], $data['description'], $data['refId'])) {
            return $this->json(['error' => 'Not all arguments set.']);
        }

        if ($this->validateDate($data['paymentDate'])) {
            return $this->json(['error' => 'Invalid date.']);
        }

        if ($data['amount'] < 0) {
            return $this->json(['error' => 'Invalid amount.']);
        }

        //setup loan number validdation
//        if ($data['amount'] < 0) {
//            return $this->json(['error' => 'Invalid amount.']);
//        }

        try {
            $payment = $doctrine->getRepository(Payment::class)->find($data['refId']);
            if ($payment) {
                return $this->json(['error' => 'Duplicate entry.']);
            }
        } catch (Exception $ex) {
            return $this->json(['error' => $ex->getMessage()]);
        }

        $this->assignPayment($data, $doctrine);

        return $this->json(['message' => 'Payment successful.']);
    }

    public function assignPayment($data, ManagerRegistry $doctrine) 
    {
        $loan = $doctrine->getRepository(Loan::class)->find($data['description']);
        $paidAmount = $data['amount'];
        $amountToPay = $loan->getAmountToPay();

        if ($paidAmount == $amountToPay) {
            $this->createPayment($data, Payment::ASSIGNED, $doctrine);
            $this->updateLoan($paidAmount, Loan::PAID, $doctrine);
        } else if ($paidAmount > $amountToPay) {
            $this->createPayment($data, Payment::PARTIALLY_ASSIGNED, $doctrine);
            $this->queuePaymentOrder($data, $paidAmount - $amountToPay, PaymentOrder::IN_PROGRESS, $doctrine);
            $this->updateLoan($amountToPay, $loan, Loan::PAID, $doctrine);
        } else {
            $this->createPayment($data, Payment::ASSIGNED, $doctrine);            
            $this->updateLoan($paidAmount, Loan::ACTIVE, $doctrine);
        }
    }

    private function createPayment($data, $state, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $payment = new Payment();
        $payment->setId($data['refId']);
        $payment->setAmount($data['amount']);
        $payment->setDescription($data['description']);
        $payment->setNationalSecurityNumber($data['nsn'] ?? null);
        $payment->setPayerName($data['firstname']);
        $payment->setPayerSurname($data['lastname']);
        $payment->setPaymentDate($data['paymentDate']);
        $payment->setPaymentResponse($data['paymentRsponse'] ?? null);
        $payment->setState($state);

        $entityManager->persist($payment);
        $entityManager->flush();
    }
    
    private function updateLoan($paidAmount, Loan $loan, $state, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $loan->setAmountIssued($loan->getAmountIssued() + $paidAmount);
        $loan->setAmountToPay($loan->getAmountToPay() - $paidAmount);
        $loan->setState($state);
        
        $entityManager->persist($loan);
        $entityManager->flush();        
    }
    
    private function queuePaymentOrder ($data, $refundAmount, $state, ManagerRegistry $doctrine)
    {
        $entity = $doctrine->getManager();

        $paymentOrder = new PaymentOrder();
        $paymentOrder->setAmountToRefund($refundAmount);
        $paymentOrder->setDescription($data['description']);
        $paymentOrder->setPaymentDate(date(DATE_ATOM, time()));
        $paymentOrder->setRefId($data['refId']);
        $paymentOrder->setState($state);

        $entityManager->persist($payment);
        $entityManager->flush();
    }

    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
