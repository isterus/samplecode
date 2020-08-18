<?php
namespace App\Command;

use App\AffordabilityCheck\AffordabilityCheckEngine;
use App\AffordabilityCheck\BankStatement;
use App\AffordabilityCheck\Properties;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AffordabilityCheck extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('affordability-check')
            ->setDescription('Checks what properties an applicant can afford based on input bank statement / property list.')
            ->addArgument('bank-statement', InputArgument::REQUIRED, 'Bank statement to evaluate.')
            ->addArgument('properties', InputArgument::REQUIRED, 'List of properties.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $bankStatementFile = $input->getArgument('bank-statement');
        if (!file_exists($bankStatementFile)) {
            throw new \Exception('BankStatementDoesNotExist');
        }
        $bankStatementContent = file($bankStatementFile);
        $transactions = array_slice($bankStatementContent, 11);
        $bankStatement = new BankStatement($transactions);
        $bankStatement->process();

        $propertiesFile = $input->getArgument('properties');
        if (!file_exists($propertiesFile)) {
            throw new \Exception('PropertiesDoesNotExist');
        }
        $propertiesContent = file($propertiesFile);
        $transactions = array_slice($propertiesContent, 1);
        $properties = new Properties($transactions);
        $properties->process();

        $affordabilityCheck = new AffordabilityCheckEngine(
            $bankStatement->getIncomeSum(),
            $bankStatement->getExpenseSum(),
            $properties->getPropertiesClean()
        );

        $affordabilityCheck->check();

        print_r($affordabilityCheck->getCanAfford());

        return 0;
    }
}
