<?php

namespace App\Command;

use \DateTime;
use App\Entity\Loan;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCSVCommand extends Command
{
    protected static $defaultName = 'import';
    protected static $defaultDescription = 'Import CSV payment file.';

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Pass "--file=#" argument with proper file path.')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'Provide proper file path.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getOption('file') ?? null;
        $counter = 0;

        if ($filePath) {
            $file = fopen($filePath, 'r');
            // Skipping first row.
            fgetcsv($file, 0, ',');
            while (($row = fgetcsv($file, 0, ',')) !== FALSE) {
                var_dump($row);
                $counterFlag = $this->createPayment($row);
                if ($counterFlag) $counter++;
            }
        } else {
            $io->error("--file argument required with proper file path.");
            return Command::INVALID;
        }

        $this->entityManager->flush();

        $io->success('Command succesful. Added ' . $counter . ' new payment records.');
        return Command::SUCCESS;
    }
    
    private function createPayment($data)
    {
//        IMPORTANT: No unique identified provided in example .csv file. Cannot confirm whether duplicates exist
//        if ($this->entityManager->getRepository(Payment::class)->find($data['7']) {
//            return false;
//        }

        $loan = $this->entityManager->getRepository(Loan::class)->findOneBy(['reference' => $data[5]]);
//        Some loans do not exist, cannot provide payments for them.
        if (!$loan) {
            return false;
        }

        $payment = new Payment();
        $payment->setId(Uuid::v1());
        $payment->setAmount($data[3]);
        $payment->setDescription($loan);
        $payment->setNationalSecurityNumber($data[4] ?? null);
        $payment->setPayerName($data[1]);
        $payment->setPayerSurname($data[2]);
        $payment->setPaymentDate(new DateTime(date('Y-m-d', strtotime($data[0]))));
        $payment->setPaymentReference($data[6] ?? null);
        $payment->setState(Payment::ASSIGNED);

        $this->entityManager->persist($payment);
        
        return true;
    }
}
