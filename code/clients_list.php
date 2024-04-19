<?php 
	
	include("connector.php");
	include("defines.php");
	
	$query = "
		SELECT 
			* 
		FROM 
			clientes
		WHERE
			cli_nome LIKE '%".$_POST['client']."%'
			AND cli_id > 1
		ORDER BY
			cli_nome ASC
	";
	
	$clientes = NULL;
	$result = $conn->query($query);
	if($result->num_rows > 0)
	{
		$i = 0;
		while($row = $result->fetch_assoc()) 
		{
			$clientes[$i]['id'] = $row['cli_id'];
			$clientes[$i]['value'] = utf8_encode($row['cli_nome']);
			$clientes[$i]['label'] = utf8_encode($row['cli_nome']);
			$i++;
		}
	}	
	$conn->close();
	echo json_encode($clientes);
		
	
	
	
?>