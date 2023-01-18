<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShowPaymentsCommand extends Command
{
    private $entityManager;
    protected static $defaultName = 'report';
    protected static $defaultDescription = 'Shows payments by date.';

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Pass "--date=#" argument to show payments by specific date.')
            ->addOption('date', null, InputOption::VALUE_REQUIRED, 'Shows payments by date. Input in YYYY-MM-DD format.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $date = $input->getOption('date') ?? null;
        $payments = array();
        $output = null;

        if ($date) {
            $query = $this->entityManager->createQuery(
                    'SELECT p
                    FROM App\Entity\Payment p 
                    WHERE p.paymentDate = \''.$date.'\''
                );
            $output = $query->getArrayResult();
        } else {
            $io->error("--date argument required with YYYY-MM-DD format.");
            return Command::INVALID;
        }

        if ($output) {
            foreach($output as $payment) {
                var_dump($payment);
            }
        } else {
            echo "\nNo results.\n";
        }

        $io->success('Command succesful.');
        return Command::SUCCESS;
    }
}
