<?php 
	include("connector.php");
	
	$dt_field_1 = isset($_POST['dt_field_1']) ? $_POST['dt_field_1'] : '00000'; // Define o valor padrão para dt_field_1 como '00000' se estiver desabilitado
	
	$query = "
		SELECT 
			odp_id
		FROM
			ordens_de_producao
			LEFT JOIN planos_de_producao ON odp_plano_de_producao = pdp_id
		WHERE
			DATE(NOW()) = pdp_data
			AND odp_cliente = '".$_POST['client_id']."'
			AND odp_produto = '".$_POST['product_id']."'
	";
	
	$result = $conn->query($query);
	
	$insidePlan = $result->num_rows > 0 ? 1 : 0;
	
	$query = "
		INSERT INTO chamados
		(
			cha_tipo,
			cha_local,
			cha_cliente,
			cha_produto,
			cha_DT,
			cha_descricao,
			cha_status,
			cha_data_hora_abertura,
			cha_operador,
			cha_plano
		)
		VALUES
		(
			".$_POST['call_type'].",
			'".$_POST['local_value']."',
			'".$_POST['client_id']."',
			'".$_POST['product_id']."',
			'".$dt_field_1."',
			UPPER('".utf8_decode($_POST['call_description'])."'),
			1,
			NOW(),
			'".utf8_decode($_POST['user_call'])."',
			'".$insidePlan."'
		)
	";
	
	if($conn->query($query) === TRUE) 
	{
		$result_insert['success'] = true;
		$result_insert['error'] = 0;
		$conn->close();
		echo json_encode($result_insert);
		exit();
	}
	else
	{
		$logfile = fopen("log.txt", "w");
		fwrite($logfile, $query."\n\n");
		fclose($logfile);
		
		$result_insert['success'] = false;
		$result_insert['error'] = 2;
		$conn->close();
		echo json_encode($result_insert);
		exit();
	}
?>
