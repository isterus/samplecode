<?php


namespace App\AffordabilityCheck;


class Properties
{

    const PROPERTY_ID = 0;
    const PROPERTY_PRICE = 2;

    private $properties = [];
    private $propertiesClean = [];
    public function __construct($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return array
     */
    public function getPropertiesClean(): array
    {
        return $this->propertiesClean;
    }

    public function process()
    {
        foreach ($this->properties as $propertyLine) {
            $property = str_getcsv($propertyLine);
            $this->propertiesClean[] = [
                $property[self::PROPERTY_ID],
                (float)$property[self::PROPERTY_PRICE]
            ];
        }
    }
}