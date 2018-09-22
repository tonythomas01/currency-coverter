<?php

function fetchAndUpdateConversionData($conversionData)
{
    global $dbConnection;
    // Now create a cache to avoid multiple insertions
    $exchangeCache = array();
    foreach ($conversionData as $conversionExchange) {
        $exchangeCache[] = "('$conversionExchange->currency', '$conversionExchange->rate')";
    }

    // Hack to update in 1 query. Ref: https://stackoverflow.com/a/35727922/3355893
    $updateDBQuery = "INSERT INTO `currency_exchange` (currency, exchange_value) VALUES " .
                     implode(', ', $exchangeCache) . " ON DUPLICATE KEY UPDATE exchange_value = 
				 VALUES(exchange_value)";
    return $dbConnection->query($updateDBQuery);
}
