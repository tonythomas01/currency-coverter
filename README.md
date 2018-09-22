# currency-coverter
A PHP maintenance script to perform currency conversion based on XML data.


# Run the script 
The script `currencyConverterScript.php` accepts two format of input strings. They are: 

- Given an amount of a foreign currency, convert it into the equivalent in US dollars. For example:
input: 'JPY 5000'
output: 'USD 65.63'
```
$ php currencyConverterScript.php 
Enter currency to convert: 
'JPY 5000'
'USD 65.625'
```
- Given an array of amounts in foreign currencies, return an array of US equivalent amounts in the same order. For example:
input: array( 'JPY 5000', 'CZK 62.5' )
output: array( 'USD 65.63', 'USD 3.27' )

```
$ php currencyConverterScript.php 
Enter currency to convert: 
array( 'JPY 5000', 'CZK 62.5' )
array( 'USD 65.625', 'USD 3.24375' )➜
```

# Run tests 
```
composer update
./vendor/bin/phpunit  tests/CurrencyConversionTest.php 
``` 
