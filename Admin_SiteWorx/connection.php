<?php
/*
// mysql_connect("database-host", "username", "password")
$conn = mysql_connect("localhost","root","root") 
			or die("cannot connected");

// mysql_select_db("database-name", "connection-link-identifier")
@mysql_select_db("test2",$conn);
*/

/**
 * mysql_connect is deprecated
 * using mysqli_connect instead
 */

$databaseHost = 'localhost';
$databaseName = 'siteworx_db';
$databaseUsername = 'root';
$databasePassword = '';
// $databaseUsername = 'siteworx_db';
// $databasePassword = ';wzK6ox89F-U';

try {
	$dsn = "mysql:host={$databaseHost};dbname={$databaseName};charset=utf8mb4";
	$pdo = new PDO($dsn, $databaseUsername, $databasePassword, [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	]);
} catch (PDOException $e) {
	die('Database connection failed: ' . $e->getMessage());
}

// Backwards-compatible alias for legacy code that expects $mysqli
$mysqli = null;
?>
