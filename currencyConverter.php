<?php
/**
 * The file is formatted with https://github.com/FriendsOfPHP/PHP-CS-Fixer
 */

include 'settings/database.php';
include 'CurrencyExchange.php';


function retrieveConversionData()
{
    $conversionAPI = "https://wikitech.wikimedia.org/wiki/Fundraising/tech/Currency_conversion_sample?ctype=text/xml&action=raw";
    $conversionXML = simplexml_load_file($conversionAPI);
    return $conversionXML->children();
}

function updateTableWithConversionData(mysqli $dbConnection, $conversionData)
{
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

/**
 * Accept a string of type 'JPY 5000' and gives back the USD equivallent
 *
 * @param $dbConnection
 * @param $inputString
 * @return float|int
 */
function doCalculateExchangeForInput($dbConnection, $inputString)
{
    $inputParts = explode(" ", trim($inputString));

    // The following strips trailing and leading \' from inputs
    $currency = explode("'", $inputParts[0])[1];
    $amount = explode("'", $inputParts[1])[0];
    $currency = CurrencyExchange::newFromName($dbConnection, $currency);
    return $currency->calculateEquivalentInUSD((float)$amount);
}

function extractExchangeFromArray($dbConnection, $line)
{
    if (preg_match("/array\((.*?)\)/", $line, $match) == 1) {
        $inputStrings = explode(", ", $match[1]);
    }
    if (!$inputStrings) {
        return null;
    }
    $outputString = "array( ";
    foreach ($inputStrings as $inputString) {
        $amountInUSD = doCalculateExchangeForInput($dbConnection, $inputString);
        $outputString .= "'USD " . $amountInUSD . "', ";
    }

    // Hack to remove trailing , and space after process
    $outputString = rtrim($outputString, ", ");
    $outputString .= " )";
    return $outputString;
}

function extractExchangeFromLine($dbConnection, $line)
{
    $amountInUSD = doCalculateExchangeForInput($dbConnection, $line);
    return "'USD " . $amountInUSD  ."'";
}

// Main execution thread

$conversionData = retrieveConversionData();
if (!$conversionData) {
    die("No conversion data found");
}

$dbUpdated = updateTableWithConversionData($dbConnection, $conversionData);
if (!$dbUpdated) {
    die("Cannot update table with latest rates due to: " . mysqli_error($dbConnection));
}

echo "Enter currency to convert: \n";
$line = fgets(STDIN);
switch ($line) {
    case (preg_match("/array\( ('[A-Z ]*[0-9.]*',? )*\)/", $line) ? true: false):
        echo extractExchangeFromArray($dbConnection, $line);
        break;
    case (preg_match("/'[A-Z]* [0-9]*'/", $line) ? true: false):
        echo extractExchangeFromLine($dbConnection, $line);
        break;
}
