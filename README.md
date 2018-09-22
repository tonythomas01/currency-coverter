# currency-coverter
A PHP maintenance script to perform currency conversion based on XML data.

# Create tables
Running the script will create this already. If else,
```
$ cd settings
mysql -u <username> -p <dbname> < create_currency_exchange_table.sql
Enter password: ****
```

# Run the script 
Replace values in `settings/database.php` for you db settings. 

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
You will have to run the program once (or create the database using the sql script) before you run the tests. This is a bad design, but see `Known Issues` below. 
```
composer update
./vendor/bin/phpunit  tests/CurrencyConversionTest.php 
``` 
# Known Issues 
The unit tests need to work on a fake db following https://phpunit.de/manual/6.5/en/database.html - which is not the case right now. 
