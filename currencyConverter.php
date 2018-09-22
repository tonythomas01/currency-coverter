<?php
/**
 * The file is formatted with https://github.com/FriendsOfPHP/PHP-CS-Fixer
 */

include 'settings/database.php';
require_once 'CurrencyExchange.php';
require_once 'utils/processInputString.php';
require_once 'utils/fetchAndUpdateConversionData.php';

function retrieveConversionData()
{
    $conversionAPI = "https://wikitech.wikimedia.org/wiki/Fundraising/tech/Currency_conversion_sample?ctype=text/xml&action=raw";
    $conversionXML = simplexml_load_file($conversionAPI);
    return $conversionXML->children();
}

// Main execution thread
global $dbConnection;
$conversionData = retrieveConversionData();
if (!$conversionData) {
    die("No conversion data found");
}
$createTableQuery = file_get_contents("settings/create_currency_exchange_table.sql");
$dbConnection->query($createTableQuery);


$dbUpdated = fetchAndUpdateConversionData($conversionData);
if (!$dbUpdated) {
    die("Cannot update table with latest rates due to: " . mysqli_error($dbConnection));
}

echo "Enter currency to convert: \n";
$line = fgets(STDIN);
echo processInputLineAndPrintResults($line);
