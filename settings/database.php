<?php

$mysqlHostname = "localhost";
$mysqlUserName = "your_settings";
$mysqlPassword = "your_settings";
$mysqlDatabase = "your_settings";

$dbConnection = mysqli_connect($mysqlHostname, $mysqlUserName, $mysqlPassword, $mysqlDatabase);

if (!$dbConnection) {
    die("Cannot establish a mysql connection due to: " . mysqli_connect_error());
}
