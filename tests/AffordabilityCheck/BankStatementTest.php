<?php

namespace App\Tests\AffordabilityCheck;

use App\AffordabilityCheck\BankStatement;
use PHPUnit\Framework\TestCase;

class BankStatementTest extends TestCase
{

    public function testGetExpense()
    {

        $transaction = [
            '1st October 2016,ATM,"High Street, 11:22am",£10.00,,"£1,173.00"',
            '3rd October 2016,Direct Debit,Satellite TV/Home phone and broadband,£95.00,,"£1,078.00"',
            '8th October 2016,Standing Order,London Town Letting Services,£305.00,,£714.00',
            '',
            '3rd November 2016,Direct Debit,Satellite TV/Home phone and broadband,£95.00,,"£1,155.18"',
            '4th November 2016,Bank Credit,From flatmate for phone and broadband,,£12.50,"£1,137.95"',
        ];

        $expected = [
            '2016-10' => 400.00,
            '2016-11' => 95.00
        ];
        $bankStatement = new BankStatement($transaction);
        $this->assertSame($expected, $bankStatement->getSummaryExpense());

    }

    public function testCheckIncome()
    {
        $value = "£1,000.00";
        var_dump( (float)ltrim(str_replace(',', '', $value), '£'));
    }
}
