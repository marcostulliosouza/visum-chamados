<?php 

	include("connector.php");
	
	if($_POST['call_type'] > 1)
	{
		if($_POST['dt_field_2'])
		{
			$dt_code = $_POST['dt_field_1']."_".$_POST['dt_field_2'];		
		} 
		else
		{
			$dt_code = $_POST['dt_field_1']."_1";			
		}
	} 
	else
	{
		$dt_code = "0000_0";
	}
	

	
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
	
	$insidePlan = 0;
	
	if($result->num_rows > 0){
		$insidePlan = 1;
	}
	
	
	
	#firstly is verified if there is any call created for this product and client
	
	$query = "
		SELECT 
			cha_id
		FROM
			chamados
		WHERE
			DATE(NOW()) = DATE(cha_data_hora_abertura)
			AND cha_cliente = '".$_POST['client_id']."'
			AND cha_produto = '".$_POST['product_id']."'
			AND cha_status != 3
	";
	
	$result = $conn->query($query);

	if($result->num_rows != 0 && $insidePlan)
	{
		$result_insert['success'] = false;
		$result_insert['error'] = 1;
		$conn->close();
		echo json_encode($result_insert);
		exit();
	}
	
	
	
	
	
	
	
	
	$query = "
		INSERT INTO chamados
		(
			cha_tipo,
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
			'".$_POST['client_id']."',
			'".$_POST['product_id']."',
			'".$dt_code."',
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
		$result_instert['error'] = 0;
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