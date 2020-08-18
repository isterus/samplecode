<?php


namespace App\AffordabilityCheck;


class BankStatement
{

    const BANK_CREDIT = 'Bank Credit';
    const DIRECT_DEBIT = 'Direct Debit';
    const STANDING_ORDER = 'Standing Order';

    const TRANSACTION_DATE = 0;
    const TRANSACTION_NAME = 1;
    const TRANSACTION_DESCRIPTION = 2;
    const TRANSACTION_EXPENSE_AMOUNT = 3;
    const TRANSACTION_INCOME_AMOUNT = 4;

    private $income = [self::BANK_CREDIT];
    private $expense = [self::DIRECT_DEBIT, self::STANDING_ORDER];
    private $expenseSum = 0.00;
    private $incomeSum = 0.00;


    /**
     * @return float
     */
    public function getExpenseSum(): float
    {
        return $this->expenseSum;
    }

    /**
     * @return float
     */
    public function getIncomeSum(): float
    {
        return $this->incomeSum;
    }

    /**
     * @array
     */
    private $transactions = [];

    /**
     * BankStatement constructor.
     * @param array $transactions
     * @throws \Exception
     */
    public function __construct(array $transactions)
    {
        $this->transactions = $transactions;
    }

    public function process()
    {
        $this->expenseSum = $this->getSummaryExpense();
        $this->incomeSum = $this->getSummaryIncome();
    }

    private function getSummaryExpense() : float
    {
        $expense = [];
        foreach ($this->transactions as $transactionLine) {
            if ($transactionLine == '') {
                continue;
            }
            $transaction = str_getcsv($transactionLine);
            if ($this->isExpense($transaction)) {
                $date = new \DateTime($transaction[self::TRANSACTION_DATE]);
                $month = $date->format('Y-m');
                if (!isset($expense[$month])) {
                    $expense[$month] = 0;
                }
                $expense[$month] += $this->cleanValue($transaction[self::TRANSACTION_EXPENSE_AMOUNT]);
            }
        }

        return array_sum($expense) / 2;
    }

    private function getSummaryIncome() : float
    {
        $incomeList = [];
        foreach ($this->transactions as $transactionLine) {
            if ($transactionLine == '') {
                continue;
            }
            $transaction = str_getcsv($transactionLine);
            if ($this->isIncome($transaction)) {
                $date = new \DateTime($transaction[self::TRANSACTION_DATE]);
                $month = $date->format('Y-m');
                $incomeList[$month][] = $transaction;

            }
        }
        $intersection = $this->getRecurringTransactions($incomeList);

        return array_reduce($intersection, function ($carry, $item) {
            $carry += $this->cleanValue($item[self::TRANSACTION_INCOME_AMOUNT]);
            return $carry;
        });
    }

    public function getRecurringTransactions(array $incomeList) : array
    {
        $incomeListPlain = array_values($incomeList);
//        var_dump($incomeListPlain);
        return array_uintersect($incomeListPlain[0], $incomeListPlain[1], function ($array1, $array2)
            {
//                echo $array1[self::TRANSACTION_DESCRIPTION] . ' ' . $array2[self::TRANSACTION_DESCRIPTION] . "\n";
                if ($array1[self::TRANSACTION_DESCRIPTION] == $array2[self::TRANSACTION_DESCRIPTION]) {
                    return 0;
                }

                if ($array1[self::TRANSACTION_DESCRIPTION] > $array2[self::TRANSACTION_DESCRIPTION]) return 1;
                return -1;

            }
        );
    }

    private function isExpense(array $transaction) : bool
    {
        return isset($transaction[self::TRANSACTION_NAME]) && in_array($transaction[self::TRANSACTION_NAME], $this->expense);
    }

    private function isIncome(array $transaction) : bool
    {
        return isset($transaction[self::TRANSACTION_NAME]) && in_array($transaction[self::TRANSACTION_NAME], $this->income);
    }

    private function cleanValue(string $value) : float
    {
        return (float)(str_replace([',', '£'], '', $value));
    }

}