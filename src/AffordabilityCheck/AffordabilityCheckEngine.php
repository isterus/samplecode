<?php


namespace App\AffordabilityCheck;


class AffordabilityCheckEngine
{
    private $income;
    private $expense;
    private $properties;

    private $canAfford = [];
    public function __construct($income, $expense, $properties)
    {
        $this->income = $income;
        $this->expense = $expense;
        $this->properties = $properties;
    }

    public function check()
    {
        foreach ($this->properties as $property) {
            $rent = $property[1];

            if ($rent * 125 / 100 < $this->income - $this->expense) {
                $this->canAfford[] = $property[0];
            }
        }
    }

    /**
     * @return array
     */
    public function getCanAfford(): array
    {
        return $this->canAfford;
    }
}