<?php 
	define("LDAP_SERVIDOR", "172.17.1.252");
	define("LDAP_DOMINIO",	"grupovisum");

	header('Content-Type: application/json');
	
	$file_data = file_get_contents("./blocklist.txt");
	$userlist = explode(";\r\n", $file_data);
	
	foreach ($userlist as $blocked_user)
	{
		if(strcmp($_POST['user'], $blocked_user) == 0)
		{
			echo json_encode(array("logged" => -2)); 
			exit();
		}			
	}
	
	
	if(!empty($_POST['user']) && !empty($_POST['pass']))
	{	
		
		$ldap = ldap_connect("ldap://".LDAP_SERVIDOR);
		$user = $_POST['user'];
		$pass = $_POST['pass'];
		
		
		$ldaprdn = LDAP_DOMINIO."\\".$user;
		
		$bind = @ldap_bind($ldap, $ldaprdn, $pass);
		
		if($bind)
		{
			echo json_encode(array("logged" => 1));
		}
		else
		{
			echo json_encode(array("logged" => 0));			
		}
	}
	else
	{
		echo json_encode(array("logged" => -1));		
	}
	
?>