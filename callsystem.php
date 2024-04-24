<?php
	if(!isset($_COOKIE['user']) || !isset($_COOKIE['time']))
	{
		echo "
			<script>
				window.location.href = 'index.php';
			</script>
		";
	
		exit();
	}
	else
	{
		$actual_time = time();
		$time_diff = $actual_time - $_COOKIE['time'];
		
	
		//if the user is logged more than 600 seconds
		if($time_diff > 6000)
		{
			echo "
				<script>
					window.location.href = 'index.php';
				</script>
			";
			exit();			
		}
	}
?>

<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="jqueryUI/jquery-ui.min.css">
	<link rel="stylesheet" href="css/extra-style-callsystem.css">
	<link rel="stylesheet" href="DataTables/media/css/dataTables.jqueryui.min.css">
	
	<script src="external/jquery/jquery.js"></script>
	<script src="jslibs/js.cookie.js"></script>
	<script src="jslibs/jquery.periodic.js"></script>
	<script src="jslibs/jquery.validate.js"></script>
		
	<script src="jslibs/ui.datepicker-pt-BR.js"></script>
	
	<script src="jqueryUI/jquery-ui.js"></script>
	<style>
		.ui-autocomplete {
			max-height: 200px;
			overflow-y: auto;
			overflow-x: hidden;
		}
		* html .ui-autocomplete {
			height: 200px;
		}
	</style>
	
	
</head>
<body>
	<div id="body_wrapper" name="body_wrapper">
		<div id="welcome_title" class="ui-corner-all"><strong>Sistema de Chamadas Engenharia de Testes</strong><br><br>Bem Vindo, @<?=$_COOKIE['user']?></div>
		<div id="tabs">
			<ul id="nav_ul">
				<li><a href="#novo_chamado"><span class="ui-icon ui-icon-plusthick"></span><strong>&nbsp;&nbsp;&nbsp;Novo Chamado</strong></a></li>
				<li><a href="#meus_chamados"><span class="ui-icon ui-icon-clipboard"></span><strong>&nbsp;&nbsp;&nbsp;Chamados Abertos</strong></a></li>
				<li><a href="#relatorio"><span class="ui-icon ui-icon-note"></span><strong>&nbsp;&nbsp;&nbsp;Relatório</strong></a></li>
				<li><a href="#sair"><span class="ui-icon ui-icon-power"></span><strong>&nbsp;&nbsp;&nbsp;Sair</strong></a></li>
			</ul>
			
			<!-- div containing the form for Jig Maintainance Call-->		
			<div id="novo_chamado">
				<form method="post" accept-charset="UTF-8" action="" id="call_form">
					<input type="hidden" id="user_call" name="user_call" value=<?=$_COOKIE['user']?>>
					<div class="callsystem_form_fields">
						<div class="div_label">Tipo de Chamado: </div><span style="color: red;">*</span>
						<div class="div_form_field">
							<select name="call_type" id="call_type">
								<option value='' selected disabled></option>
							</select>
						</div>
					</div>
					<!-- adicionado campo do local do chamado -->
					<div class="callsystem_form_fields">
						<div class="div_label">Local: </div>
						<div class="div_form_field">
							<input type="hidden" id="local_id" name="local_id">
							<input name="local_field" id="local_field"><span style="color: red;">*</span>
						</div>
					</div>
					<!--  -->
					<div class="callsystem_form_fields">
						<div class="div_label">Cliente: </div>
						<div class="div_form_field">
							<input type="hidden" id="client_id" name="client_id">
							<input name="client_field" id="client_field"><span style="color: red;">*</span>
						</div>
					</div>
					<div class="callsystem_form_fields">
						<div class="div_label">Produto: </div>
						<div class="div_form_field">
							<input type="hidden" id="product_id" name="product_id">
							<input name="product_field" id="product_field"><span style="color: red;">*</span>
						</div>
					</div>
					<div class="callsystem_form_fields">
						<div class="div_label">Dispositivo de Teste: </div>
						<div class="div_form_field">
							<strong>DT-</strong><input name="dt_field_1" id="dt_field_1" placeholder="XXXXXX" maxlength="6"><span style="color: red;">*</span>
						</div>
					</div>
					
					<div class="callsystem_form_fields">
						<div class="div_label" id="description_label" name="description_label">Breve descrição sobre o Chamado: <span style="color: red;">*</span></div>
						<div class="div_form_field" style="width: 100%">
							<textarea name="call_description" id="call_description" class="ui-corner-all ui-widget-content"></textarea>
						</div>
						<div id="char_counter">255</div>
					</div>
					
					<div id="div_btn_submit"><br><br><br>
						<button id="btn_submit"><strong>Enviar</strong></button>
					</div>
					
				</form>
			</div>
			
			
			<div id="meus_chamados">
			
				<div id="my_calls_button_wrapper" name="my_calls_button_wrapper">
					<div id="my_calls_button_row" name="my_calls_button_row">
<!-- FILTER CODE -->
						<div id="status_filter" name="status_filter">
							<div id="wrapper_filter" name="wrapper_filter">
								<div id="filter_row" name="filter_row">
									<div id="label_filter" name="label_filter"><strong>Filtrar por: </strong></div>
									<div id="filter_selection" name="filter_selection">
										<input type="hidden" name="was_filtered" id="was_filtered" value="0">
										<select name="filter_options" id="filter_options">
											<option value="" selected>Não Filtrar</option>
											<option value="client">Cliente</option>
											<option value="opendate">Data de Abertura</option>
											<option value="closedate">Data de Fechamento</option>
											<option value="jig">Jiga</option>
											<option value="product">Produto</option>
											<option value="status">Status do Chamado</option>
											<option value="type">Tipo</option>
											
											
											
											
										</select>
									</div>
									<div id="search_field_filter" name="search_filter_field">
												<div class="value_field_filter" id="type_field_filter" name="type_field_filter">
													<select name="type_value_filter" id="type_value_filter">
														<option value="" selected disabled></option>
													</select>
												</div>
												<div class="value_field_filter" id="client_field_filter" name="client_field_filter">
													<input id="client_value_filter" name="client_value_filter">
												</div>
												<div class="value_field_filter" id="product_field_filter" name="product_field_filter">
													<input id="product_value_filter" name="product_value_filter">
												</div>
												<div class="value_field_filter" id="jig_field_filter" name="jig_field_filter">
												<strong>DT- </strong><input name="dt_field_filter_1" id="dt_field_filter_1" placeholder="XXXXXX" maxlength="6">
												</div>
												<div class="value_field_filter" id="open_field_filter" name="open_field_filter">
													<input id="opendate_field_filter" name="opendate_field_filter" readonly>
												</div>
												<div class="value_field_filter" id="close_field_filter" name="close_field_filter">
													<input id="closedate_field_filter" name="closedate_field_filter" readonly>
												</div>
												<div class="value_field_filter" id="status_field_filter" name="status_field_filter">
													<select name="status_value_filter" id="status_value_filter">
														<option value="" selected disabled></option>
														<option value="1">Aberto</option>
														<option value="2">Em atendimento</option>
														<option value="3">Encerrado</option>
													</select>
												</div>
											<div class="white_space_div">&nbsp;</div>
									</div>
									<div id="button_filter" name="button_filter">
										<div id="wrapper_button_filter" name="wrapper_button_filter">
											<button id="btn_filter" name="btn_filter"><strong>Filtrar</strong></button>
										</div>
										<div class="white_space_div">&nbsp;</div>
									</div> 
								</div>
							</div>
						</div>
<!-- FILTER CODE -->						
						<div id="button_back" name="button_back">
							<button id="btn_backward"><strong>Anterior</strong></button>
						</div>
						<div id="paging_value" name="paging_value" class="ui-corner-all">&nbsp;</div>
						<div id="button_next" name="button_next">
							<button id="btn_forward"><strong>Próxima</strong></button>
						</div>
					</div>
				</div><br>
				<div id="my_calls_wrapper" name="my_calls_wrapper">
					
				</div>
			</div>
			
			<div id="relatorio">
<!-- REPORT FILTER CODE -->
				<div id="report_filter_wrapper" name="report_filter_wrapper">
					<input type="hidden" id="report_filtering" name="report_filtering" value="0">
					<div id="report_filter_first_row" name="report_filter_first_row">
						<div id="date_report_filter_label" name="date_report_filter_label"><strong>Data de Abertura: </strong></div>
						<div id="dates_cell_report_filter" name="dates_cell_report_filter">
							<div id="date_wrapper_report_filter" name="date_wrapper_report_filter">
								<div id="date_table_row_report_filter" name="date_table_row_report_filter">
									<div id="open_date_field_report_filter" name="open_date_field_report_filter">
										<input id="opendate_field_report_filter" name="opendate_field_report_filter" readonly>
									</div>
									<div id="until_label_report_filter" name="until_label_report_filter"><strong>até</strong></div>
									<div id="close_date_field_report_filter" name="close_date_field_report_filter">
										<input id="closedate_field_report_filter" name="closedate_field_report_filter" readonly>
									</div>
								</div>
							</div>
						</div>
						<div id="client_report_filter_label" name="client_report_filter_label"><strong>Cliente: </strong></div>
						<div id="client_report_filter_field" name="client_report_filter_field">
							<input id="client_value_report_filter" name="client_value_report_filter">
						</div>
						<div id="product_report_filter_label" name="product_report_filter_label"><strong>Produto: </strong></div>
						<div id="product_report_filter_field" name="product_report_filter_field">
							<input id="product_value_report_filter" name="product_value_report_filter">
						</div>
						<div id="spacer_first_row_report_filter" name="spacer_first_row_report_filter">&nbsp;</div>
					</div>
					<div id="report_filter_second_row" name="report_filter_second_row">
						<div id="creator_report_filter_label" name="creator_report_filter_label"><strong>Aberto por: </strong></div>
						<div id="creator_report_filter_field" name="creator_report_filter_field">
							<input id="creator_value_report_filter" name="creator_value_report_filter">
						</div>
						<div id="responsible_report_filter_label" name="responsible_report_filter_label"><strong>Responsável: </strong></div>
						<div id="responsible_report_filter_field" name="responsible_report_filter_field">
							<input id="responsible_value_report_filter" name="responsible_value_report_filter">
						</div>
						<div id="empty_cell_report_filter" name="empty_cell_report_filter">
							<button id="btn_report_filter_clear" name="btn_report_filter_clear"><strong>Limpar</strong></button>
						</div>
						<div id="buttons_report_filter" name="buttons_report_filter">
							<button id="btn_report_filter" name="btn_report_filter"><strong>Filtrar</strong></button>&nbsp;
							<button id="btn_report_filter_save" name="btn_report_filter_save"><strong>Salvar</strong></button>
						</div>
					</div>
				</div><br>
<!-- END REPORT FILTER CODE -->
				<div id="report_table_wrapper" name="report_table_wrapper">
					<table class="report_table" id="report_calls_table" name="report_calls_table">
						<thead>
							<tr>
								<td width="130">Data de Abertura</td>
								<td width="80">Atraso</td>
								<td width="80">Atendimento</td>
								<td width="80">Total</td>
								<td width="110">Criado Por</td>
								<td width="80">Local</td>
								<td width="120">Cliente</td>
								<td width="120">Produto</td>
								<td width="120">Atendido Por</td>
								<td>Resposta</td>								
							</tr>
						</thead>
						<tbody id="body_report_table">
							
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
	<script src="jslibs/functions_sys.js"></script>
</body>