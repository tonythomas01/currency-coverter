<?php

/**
 * Accept a string of type 'JPY 5000' and gives back the USD equivallent
 *
 * @param $inputString
 * @return float|int
 */
function doCalculateExchangeForInput($inputString)
{
    $inputParts = explode(" ", trim($inputString));

    // The following strips trailing and leading \' from inputs
    $currency = explode("'", $inputParts[0])[1];
    $amount = explode("'", $inputParts[1])[0];
    $currency = CurrencyExchange::newFromName($currency);
    return $currency->calculateEquivalentInUSD((float)$amount);
}

function extractExchangeFromArray($line)
{
    if (preg_match("/array\((.*?)\)/", $line, $match) == 1) {
        $inputStrings = explode(", ", $match[1]);
    }
    if (!$inputStrings) {
        return null;
    }
    $outputString = "array( ";
    foreach ($inputStrings as $inputString) {
        $amountInUSD = doCalculateExchangeForInput($inputString);
        $outputString .= "'USD " . $amountInUSD . "', ";
    }

    // Hack to remove trailing , and space after process
    $outputString = rtrim($outputString, ", ");
    $outputString .= " )";
    return $outputString;
}

function extractExchangeFromLine($line)
{
    $amountInUSD = doCalculateExchangeForInput($line);
    return "'USD " . $amountInUSD  ."'";
}

function processInputLineAndPrintResults($line)
{
    switch ($line) {
        case (preg_match("/array\( ('[A-Z ]*[0-9.]*',? )*\)/", $line) ? true: false):
            $output = extractExchangeFromArray($line);
            break;
        case (preg_match("/'[A-Z]* [0-9]*'/", $line) ? true: false):
            $output = extractExchangeFromLine($line);
            break;
    }
    return $output;
}
