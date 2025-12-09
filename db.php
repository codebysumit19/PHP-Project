<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "alldata";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed");
}

// Ensure proper encoding for all queries
$conn->set_charset("utf8mb4");
