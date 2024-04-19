<?php
	include("connector.php");
	include("defines.php");
	
	$query = "
		SELECT 
			* 
		FROM 
			tipos_chamado
		WHERE
			tch_id != '5'
	";
	
	$result = $conn->query($query);
	if($result->num_rows > 0)
	{
		$i = 0;
		while($row = $result->fetch_assoc()) 
		{
			$tipos_chamado[$i]['id'] = $row['tch_id'];
			$tipos_chamado[$i]['descricao'] = utf8_encode($row['tch_descricao']);
			$i++;
			}
	}	
	$conn->close();
	echo json_encode($tipos_chamado);
		
	
?>