<?php
	include("connector.php");
	include("defines.php");
	
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

	$query = "
		SELECT 
			COUNT(cha_id) AS number_of_calls 
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
	";

	$result = $conn->query($query);
	if($result->num_rows > 0)
	{
		$row = $result->fetch_assoc(); 
		$total_calls = $row['number_of_calls'];
		
	}
	else
	{
		$conn->close();
		exit();
	}
	
	if(($total_calls % CALLS_PER_PAGE) > 0)
	{
		$dados["page"] = 1;
		$dados["total"] = floor($total_calls / CALLS_PER_PAGE) + 1;
	}
	else
	{
		$dados["page"] = 1;
		$dados["total"] = ($total_calls / CALLS_PER_PAGE);
		if($total_calls <= 0)
		{
			$dados["total"] = 1;
		}
	}
	$conn->close();
	echo json_encode($dados);
	
	
?>