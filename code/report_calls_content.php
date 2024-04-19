<?php
	include("connector.php");
	include("defines.php");
	
	
	$filter = "";
	if(isset($_POST["filter"]))
	{
		//print_r($_POST);
		$begin_date_array = explode("/", $_POST['begin_date']);
		$filter_begin_date = date( 'Y-m-d', strtotime($begin_date_array[1]."/".$begin_date_array[0]."/".$begin_date_array[2]));
		
		$end_date_array = explode("/", $_POST['end_date']);
		$filter_end_date = date( 'Y-m-d', strtotime($end_date_array[1]."/".$end_date_array[0]."/".$end_date_array[2]));
		
		$filter .= "AND DATE(cha_data_hora_abertura) >= DATE('".$filter_begin_date."') 
			AND DATE(cha_data_hora_abertura) <= DATE('".$filter_end_date."') ";
		
		if(isset($_POST['client']) and $_POST['client'] != "")
		{
			$filter .= "AND cli_nome LIKE '%".$_POST['client']."%' ";
		}

		if(isset($_POST['product']) and $_POST['product'] != "")
		{
			$filter .= "AND pro_nome LIKE '%".$_POST['product']."%' ";
		}	
		
		if(isset($_POST['creator']) and $_POST['creator'] != "")
		{
			$filter .= "AND cha_operador LIKE '%".$_POST['creator']."%' ";
		}	
		
		if(isset($_POST['support']) and $_POST['support'] != "")
		{
			$filter .= "AND col_nome LIKE '%".$_POST['support']."%' ";
		}	
		//echo $filter;
	}
	else
	{
		$filter = "AND DATE(cha_data_hora_abertura) >=  (CURDATE() - INTERVAL 1 DAY)
			AND DATE(cha_data_hora_abertura) <=  CURDATE() ";
	}
	
	
	
	$sql = "
		SELECT 
			cha_id,
			cha_operador,
			IF(cha_status < 3, TIMESTAMPDIFF(MINUTE, cha_data_hora_abertura, NOW()), TIMESTAMPDIFF(MINUTE, cha_data_hora_abertura, cha_data_hora_termino)) AS duracao_total,
			IF(cha_status = 1, 0, IF(cha_status = 2, TIMESTAMPDIFF(MINUTE, cha_data_hora_atendimento, NOW()), TIMESTAMPDIFF(MINUTE, cha_data_hora_atendimento, cha_data_hora_termino))) AS duracao_atendimento,
			cha_tipo,
			cha_status,
			cli_nome,
			pro_nome,
			cha_DT,
			atc_colaborador,
			col_nome,
			cha_descricao,
			cha_plano,
			DATE_FORMAT(cha_data_hora_abertura, '%d/%m/%Y %H:%i') AS cha_data_hora_abertura,
			cha_data_hora_atendimento,
			cha_data_hora_termino,
			ach_descricao,
			dtr_descricao
		FROM 
			chamados 
			LEFT JOIN (SELECT atc1.* FROM atendimentos_chamados atc1 JOIN (SELECT atc_chamado, MAX(atc_data_hora_inicio) atc_inicio FROM atendimentos_chamados GROUP BY atc_chamado) atc2 ON atc1.atc_chamado = atc2.atc_chamado AND atc1.atc_data_hora_inicio = atc2.atc_inicio) atc ON atc.atc_chamado = cha_id
			LEFT JOIN clientes ON cha_cliente = cli_id
			LEFT JOIN produtos ON cha_produto = pro_id
			LEFT JOIN colaboradores ON col_id = atc_colaborador
			LEFT JOIN acoes_chamados ON cha_acao = ach_id
			LEFT JOIN detratores ON ach_detrator = dtr_id
		WHERE 
			1 = 1
			".$filter."
		GROUP BY
			cha_id
		ORDER BY 
			cha_id DESC
		
	";	
	
	//echo $sql."\n\n<br><br>";
	$result = $conn->query($sql);
	if($result->num_rows > 0)
	{
		$i = 0;
		while($row = $result->fetch_assoc()) 
		{
			
			foreach($row as $key => $value) 
			{
				$row[$key] = utf8_encode($value);
			}
			$tipos_chamado[$i] = $row;
			$i++;
		}
		//print_r($tipos_chamado);
		echo json_encode($tipos_chamado);

	}
	else
	{
		echo $conn->error;
	}
	
	
	
?>