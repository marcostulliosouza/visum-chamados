<?php 
	
	include("connector.php");
	include("defines.php");
	
	$query = "
		SELECT 
			* 
		FROM 
			produtos
		WHERE
			pro_cliente = '".$_POST['client']."'
			AND pro_nome LIKE '%".$_POST['product']."%'
			AND pro_id > 1
			AND pro_ativo = 1
			AND pro_com_teste = 1
		ORDER BY
			pro_nome ASC
	";
	
	$produtos = NULL;
	$result = $conn->query($query);
	if($result->num_rows > 0)
	{
		$i = 0;
		while($row = $result->fetch_assoc()) 
		{
			$produtos[$i]['id'] = $row['pro_id'];
			$produtos[$i]['value'] = utf8_encode($row['pro_nome']);
			$produtos[$i]['label'] = utf8_encode($row['pro_nome']);
			$i++;
		}
	}	
	$conn->close();
	echo json_encode($produtos);
		
	
?>