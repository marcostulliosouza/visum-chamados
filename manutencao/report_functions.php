<?php
	include("connector.php");
		
	$query = "
		SELECT 
			lmd_id,
			lmd_observacao,
			DATE_FORMAT(lmd_data_hora_inicio, '%d/%m/%Y %H:%i') as lmd_data_hora_inicio,
			TIMESTAMPDIFF(MINUTE, lmd_data_hora_inicio, lmd_data_hora_fim) AS duracao_total,
			dis_id,
			dis_descricao,
			col_nome
		FROM 
			log_manutencao_dispositivo
			LEFT JOIN dispositivos ON lmd_dispositivo = dis_id
			LEFT JOIN colaboradores ON col_id = lmd_colaborador
		ORDER BY
			lmd_id DESC
	";
	
	$logs_manutencao = NULL;
	$result = $conn->query($query);
	if($result->num_rows > 0)
	{
		$i = 0;
		while($row = $result->fetch_assoc()) 
		{
			$logs_manutencao[$i]['data_inicio'] = $row['lmd_data_hora_inicio'];
			$logs_manutencao[$i]['duracao'] = $row['duracao_total'];
			$logs_manutencao[$i]['colaborador'] = utf8_encode($row['col_nome']);
			$logs_manutencao[$i]['codigo_dispositivo'] = $row['dis_id'];
			$logs_manutencao[$i]['descricao_dispositivo'] = utf8_encode($row['dis_descricao']);
			$logs_manutencao[$i]['observacao'] = utf8_encode($row['lmd_observacao']);
			$query = "
				SELECT
					ifm_descricao,
					rif_ok,
					rif_observacao
				FROM
					resposta_item_formulario
					LEFT JOIN itens_formulario_manutencao ON ifm_id = rif_item
				WHERE 
					rif_log_manutencao = '".$row['lmd_id']."'
				ORDER BY
					ifm_posicao
			";
			
			$result_items = $conn->query($query);
			if($result_items->num_rows > 0)
			{
				$j = 0;
				while($row_item = $result_items->fetch_assoc()) 
				{
					$logs_manutencao[$i]['itens'][$j]['descricao'] = utf8_encode($row_item['ifm_descricao']);
					$logs_manutencao[$i]['itens'][$j]['situacao'] = ($row_item['rif_ok'] == 1)? "OK" : "NOK";
					$logs_manutencao[$i]['itens'][$j]['observacao'] = utf8_encode($row_item['rif_observacao']);
					$j++;
				}
			}
			$i++;
		}
	}	
	$conn->close();
?>