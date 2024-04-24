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
			chamados.cha_id, 
			clientes.cli_nome, 
			produtos.pro_nome, 
			chamados.cha_DT, 
			chamados.cha_descricao, 
			tipos_chamado.tch_descricao,
			chamados.cha_status,
			DATE_FORMAT(chamados.cha_data_hora_abertura, '%d/%m/%Y %H:%i') AS cha_abertura,  
			DATE_FORMAT(chamados.cha_data_hora_termino, '%d/%m/%Y %H:%i') AS cha_termino,
			chamados.cha_operador,
			IF(colaboradores.col_nome IS NULL, ' ', colaboradores.col_nome) AS col_nome, 
			IF(acoes_chamados.ach_descricao IS NULL, ' ', acoes_chamados.ach_descricao) AS ach_descricao,
			local_chamado.loc_nome AS cha_local_nome
		FROM 
			chamados 
			LEFT JOIN tipos_chamado ON chamados.cha_tipo = tipos_chamado.tch_id 
			LEFT JOIN clientes ON chamados.cha_cliente = clientes.cli_id
			LEFT JOIN produtos ON chamados.cha_produto = produtos.pro_id
			LEFT JOIN atendimentos_chamados ON chamados.cha_id = atendimentos_chamados.atc_chamado
			LEFT JOIN colaboradores ON atendimentos_chamados.atc_colaborador = colaboradores.col_id
			LEFT JOIN acoes_chamados ON chamados.cha_acao = acoes_chamados.ach_id
			LEFT JOIN local_chamado ON chamados.cha_local = local_chamado.loc_id
		WHERE 
			1 = 1
			AND chamados.cha_descricao NOT LIKE 'Chamado criado automaticamente%'
			".$filter."
		GROUP BY
			chamados.cha_id
		ORDER BY     
			chamados.cha_data_hora_abertura DESC,
			chamados.cha_data_hora_termino DESC
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
