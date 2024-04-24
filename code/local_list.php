<?php
	include("connector.php");
	include("defines.php");
	
	$query = "
		SELECT 
			* 
		FROM 
			local_chamado
		WHERE
			loc_nome LIKE '%".$_POST['local']."%'
		ORDER BY
			loc_nome ASC
	";
	$locais = NULL;
	$result = $conn->query($query);
	if($result->num_rows > 0)
	{
		$i = 0;
		while($row = $result->fetch_assoc()) 
		{
			$locais[$i]['id'] = $row['loc_id'];
			$locais[$i]['value'] = utf8_encode($row['loc_nome']);
			$locais[$i]['label'] = utf8_decode($row['loc_nome']);
			$i++;
		}
	}	
	$conn->close();
	echo json_encode($locais);
	
?>