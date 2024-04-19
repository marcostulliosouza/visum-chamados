<?php
	include("connector.php");
	include("defines.php");
	
	#$_POST['user'] = "leandro.biesek";
	#$_POST['page'] = 1;
	
	$filter = "";
	if(isset($_POST["filter"]))
	{
		switch($_POST["filter"])
		{
			case "client":
				$filter = " AND cli_nome LIKE '%".utf8_decode($_POST["value"])."%' ";
			break;
			case "closedate":
				$filter = " AND DATE_FORMAT(cha_data_hora_termino, '%d/%m/%Y') = '".$_POST["value"]."' ";
			break;
			case "jig":
				$filter = " AND cha_DT LIKE '%".$_POST["value"]."%' ";
			break;
			case "opendate":
				$filter = " AND DATE_FORMAT(cha_data_hora_abertura, '%d/%m/%Y') = '".$_POST["value"]."' ";
			break;
			case "product":
				$filter = " AND pro_nome LIKE '%".utf8_decode($_POST["value"])."%' ";
			break;
			case "status":
				$filter = " AND cha_status = ".$_POST["value"]." ";
			break;
			case "type":
				$filter = " AND cha_tipo = ".$_POST["value"]." ";
			break;
		}
	}
	
	$sql = "
		SELECT 
			cha_id, 
			cli_nome, 
			pro_nome, 
			cha_DT, 
			cha_descricao, 
			tch_descricao,
			cha_status,
			DATE_FORMAT(cha_data_hora_abertura, '%d/%m/%Y %H:%i') AS cha_abertura,  
			DATE_FORMAT(cha_data_hora_termino, '%d/%m/%Y %H:%i') AS cha_termino,
			cha_operador,
			IF(col_nome IS NULL, ' ', col_nome) AS col_nome, 
			IF(ach_descricao IS NULL, ' ', ach_descricao) AS ach_descricao
		FROM 
			chamados 
			LEFT JOIN tipos_chamado ON chamados.cha_tipo = tipos_chamado.tch_id 
			LEFT JOIN clientes ON chamados.cha_cliente = clientes.cli_id
			LEFT JOIN produtos ON chamados.cha_produto = produtos.pro_id
			LEFT JOIN atendimentos_chamados ON chamados.cha_id = atendimentos_chamados.atc_chamado
			LEFT JOIN colaboradores ON atendimentos_chamados.atc_colaborador = colaboradores.col_id
			LEFT JOIN acoes_chamados ON chamados.cha_acao = acoes_chamados.ach_id
		WHERE 
			1 = 1
			AND cha_descricao NOT LIKE 'Chamado criado automaticamente%'
			".$filter."
		GROUP BY
			cha_id
		ORDER BY 	
			cha_data_hora_abertura DESC,
			atc_data_hora_termino DESC
		LIMIT 
			".(($_POST['page']-1) * CALLS_PER_PAGE).", ".CALLS_PER_PAGE."
	";

	#echo $sql;
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
