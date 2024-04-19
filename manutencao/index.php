<?php 
	include("report_functions.php");
?>
<html lang="pt_br">
	<head>
		<style>
			
			.collapsible {
				background-color: #777;
				color: white;
				cursor: pointer;
				font-weight: bold;
				width: 100%;
				border: none;
				text-align: center;
				outline: none;
				font-size: 15 px;
			}
			
			.active, .collapsible:hover {
				background-color: #555;
			}
			
			.content {
				padding: 0 18px;
				display: none;
				background-color: #f1f1f1;
				color: black;
			}
			
			.alter {
				width: 100%;
			}
			
			.alter tr:nth-child(even){
				background-color: #EEE;			
			}
			.alter tr:nth-child(odd){
				background-color: #FFF;			
			}
			
		</style>
	</head>
	<body style='font-family: Calibri;'>
		<table style="width: 100%">
			<tr  style="font-weight: bold; background-color: #AAAAAA">
				<th>Data da Manutencao</td>
				<th>Duração (minutos)</td>
				<th>Colaborador</td>
				<th>Código do Dispositivo</td>
				<th>Descrição do Dispositivo</td>
			</tr>
		
<?php 
		foreach($logs_manutencao as $key => $value)
		{
			echo "
				<tr class='collapsible'>
					<td >".$value['data_inicio']."</td>
					<td style='text-align: center'>".$value['duracao']."</td>
					<td>".$value['colaborador']."</td>
					<td style='text-align: center'>".$value['codigo_dispositivo']."</td>
					<td>".$value['descricao_dispositivo']."</td>
				</tr>
				<tr style='display: none'><td colspan='5'>
			";
			if(isset($value['itens']))
			{
				echo "
					<table class='alter'>
				";
				foreach($value['itens'] as $key2 => $value2)
				{
					echo "
							<tr>
								<td>".$value2['descricao']."</td>
								<td>".$value2['situacao']."</td>
							</tr>
					";
					
					if(strlen($value2['observacao']) > 0)
					{
						echo "
							<tr class='items'>
								<td colspan='2'>OBS.:<BR>".$value2['observacao']."</td>
							</tr>
						";	
					}
					
					
				}
				echo "
						</table>
					";
			}
				
			echo "
				</td></tr>
			";
		}
?>
		</table>
		
		
	<script>
		var coll = document.getElementsByClassName("collapsible");
		var i;
		
		for(i=0; i < coll.length; i++) {
			
			coll[i].addEventListener("click", function(){
				this.classList.toggle("active");
				var content = this.nextElementSibling;
				console.log(content)
				if(content.style.display === ""){
					content.style.display = "none";
				} else {
					content.style.display = "";
				}
				
			});
		}
	
	</script>
	</body>
</html>















