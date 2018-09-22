<?php

$mysqlHostname = "localhost";
$mysqlUserName = "";
$mysqlPassword = "";
$mysqlDatabase = "";

$dbConnection = mysqli_connect($mysqlHostname, $mysqlUserName, $mysqlPassword, $mysqlDatabase);

if (!$dbConnection) {
    die("Cannot establish a mysql connection due to: " . mysqli_connect_error());
}
