<?php
	include("excelwriter.inc.php");
	
	include("connector.php");
	include("defines.php");
	
	
	function format_time($minutes)
	{
		if(isset($minutes))
		{	
			if($minutes >= 0)
			{	
				$hour = (int)($minutes / 60);
				$min = $minutes % 60;
		
				return (string)(sprintf("%02d", $hour).":".sprintf("%02d", $min));
			}
			else 
			{
				$hour = (int)(abs($minutes) / 60);
				$min = abs($minutes) % 60;
		
				return (string)(sprintf("-%02d", $hour).":".sprintf("%02d", $min));
			}
		}
	}
	
	
	
	$excel=new ExcelWriter("../Relatorio.xls");
	
	if($excel==false)	
		echo $excel->error;
	
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
			dtr_descricao,
			local_chamado.loc_nome AS cha_local
		FROM 
			chamados 
			LEFT JOIN (SELECT atc1.* FROM atendimentos_chamados atc1 JOIN (SELECT atc_chamado, MAX(atc_data_hora_inicio) atc_inicio FROM atendimentos_chamados GROUP BY atc_chamado) atc2 ON atc1.atc_chamado = atc2.atc_chamado AND atc1.atc_data_hora_inicio = atc2.atc_inicio) atc ON atc.atc_chamado = cha_id
			LEFT JOIN clientes ON cha_cliente = cli_id
			LEFT JOIN produtos ON cha_produto = pro_id
			LEFT JOIN colaboradores ON col_id = atc_colaborador
			LEFT JOIN acoes_chamados ON cha_acao = ach_id
			LEFT JOIN detratores ON ach_detrator = dtr_id
			LEFT JOIN local_chamado ON chamados.cha_local = local_chamado.loc_id
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
	$header = array("Data de Abertura", "Atraso", "Atendimento", "Total", "Criado Por", "Local", "Cliente", "Produto", "Atendido Por", "Resposta", "Ação Realizada");
	
	if($result->num_rows > 0)
	{
		$excel->writeLine($header);
		$i = 0;
		while($row = $result->fetch_assoc()) 
		{
			$excel->writeCol($row['cha_data_hora_abertura']);
			$excel->writeCol(format_time($row['duracao_total']-$row['duracao_atendimento']));
			$excel->writeCol($row['cha_status'] > 1 ? format_time($row['duracao_atendimento']) : "");
			$excel->writeCol(format_time($row['duracao_total']));
			$excel->writeCol((string)$row['cha_operador']);
			$excel->writeCol((string)$row['cha_local']); // Adiciona o campo cha_local
			$excel->writeCol((string)$row['cli_nome']);
			$excel->writeCol((string)$row['pro_nome']);
			$excel->writeCol((string)$row['col_nome']);
			$excel->writeCol((string)utf8_encode($row['dtr_descricao']));
			$excel->writeCol((string)utf8_encode($row['ach_descricao']));
			$excel->writeRow();
		}
	}
	$excel->close();
	$answer['success'] = 1;
	
	json_encode($answer);
	
?>