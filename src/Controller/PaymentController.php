<?php

namespace App\Controller;

use \DateTime;
use App\Entity\Payment;
use App\Entity\Loan;
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
            
        } else if ($paidAmount < $amountToPay) {
            
        } else {
            
        }
    }

    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
