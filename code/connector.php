<?php	
	$server = "10.161.100.11";
	$username = "bct";
	$password = "bct17";
	$dbname = "better_call_test";
	
	$conn = new mysqli($server, $username, $password, $dbname);
	if($conn->connect_error)
	{
		die("Connection failed: ".$conn->connect_error);
	}
?>