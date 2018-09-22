<?php
include_once 'settings/database.php';

/**
 * Class CurrencyExchange
 */
class CurrencyExchange
{
    private $currency;
    private $exchangeRate;

    public function __construct($currency, $exchangeRate)
    {
        $this->currency = $currency;
        $this->exchangeRate = $exchangeRate;
    }

    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    public function calculateEquivalentInUSD($amount)
    {
        return $this->exchangeRate * $amount;
    }

    public static function newFromName($currencyName)
    {
        global $dbConnection;
        $statement = "SELECT currency, exchange_value FROM currency_exchange WHERE currency = ?";
        $query = $dbConnection->prepare($statement);
        $query->bind_param("s", $currencyName);
        $query->execute();
        $query->store_result();
        if (!$query->num_rows) {
            return null;
        }
        $query->bind_result($currency, $exchangeValue);
        $query->fetch();
        $query->close();
        return new self($currency, $exchangeValue);
    }
}
