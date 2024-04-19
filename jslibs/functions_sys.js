$( document ).ready(function() {
	options = {
		period: 10000,
		decay:	1
	};
	
	$.periodic(options, function() {
		var dt = new Date();
		var time = Math.floor(dt.getTime()/1000);
		if((time - Cookies.get('time')) > 6000)
		{
			alert("Sessão expirada!");
			window.location.href = "index.php";
		}
	}); 

	//jquery for the tabs
	$( "#tabs" ).tabs();
	$( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
	$( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
	

	//exit tab
	$( "#tabs" ).on( "tabsbeforeactivate", function( e, ui ) {
		var tab_clicked = new String(ui.newPanel.selector);
		
		if(tab_clicked == "#sair")
		{	
			e.preventDefault();
			window.location.href = "index.php";
		}
	});
	
	//jQuery form 
	
	//call type
	$( "#call_type" ).selectmenu( {
		style: 'dropdown',
		change:	function(event, ui) {
			if($(this).val() > 1){
				$( "#dt_field_1" ).prop("disabled", false);
				$( "#dt_field_2" ).prop("disabled", false);
			} else {
				$( "#dt_field_1" ).prop("disabled", true);
				$( "#dt_field_2" ).prop("disabled", true);
			}
		}
	
	});
	
	//jquery to disable the input DT 
	$( "#dt_field_1" ).prop("disabled", true);
	$( "#dt_field_2" ).prop("disabled", true);
	
	//jquery for the DT field code
	$( "#dt_field_1" ).on("keypress keyup blur", function(event){
		$(this).val($(this).val().replace("/[^\d].+/", ""));
		
		if((event.which < 48) || (event.which > 57)){
			event.preventDefault();
		}
		
		var len = $(this).val().length;
		if(len >= 4){
			$( "#dt_field_2" ).focus();
		}
	});
	
	$( "#dt_field_2" ).on("keypress keyup blur", function(event){
		$(this).val($(this).val().replace("/[^\d].+/", ""));
		
		if((event.which < 48) || (event.which > 57)){
			event.preventDefault();
		}
	});
	
	
	
	
	
	//ajax call to populate de selectmenu with the call types
	$.ajax({
		type: 		"POST",
		url: 		"code/call_types_list.php",
		data: 		"",
		dataType:	"json",
		success:	function( data ) {
			$.each(data, function(index, calltype) {
				$("<option value='" + calltype.id + "'>" + calltype.descricao + "</option>").appendTo("#call_type");
			});
		}
	});
	

	//jquery for the autocomplete client field
	$( "#client_field" ).autocomplete({
		source:		function( request, response ) {
			var dados = "client=" + request.term;
			$.ajax({
				type:		"POST",
				url:		"code/clients_list.php",
				data:		dados,
				dataType:	"json",
				success:	function( data ) {
					response( data );
				}
			});
		},
		delay:		1000,
		minLength:	2,
		select:		function(event, ui) {
			$( "#client_id" ).val(ui.item.id);
			$(this).val(ui.item.value);
		},
		change: function(event, ui){
			if(!ui.item)
				$(this).val("")
		}
	});
	
	//jquery for the autocomplete product field
	$( "#product_field" ).autocomplete({
		source:		function( request, response ) {
			var dados2 = "client=" + $( "#client_id" ).val() + "&product=" + request.term;
			$.ajax({
				type:		"POST",
				url:		"code/products_list.php",
				data:		dados2,
				dataType:	"json",
				success:	function( data ) {
					response( data );
				}
			});
		},
		delay:		1000,
		minLength:	2,
		select:		function(event, ui) {
			$( "#product_id" ).val(ui.item.id);
			$(this).val(ui.item.value);
		},
		change: function(event, ui){
			if(!ui.item)
				$(this).val("")
		}
	});
	
	
	

	
	//jquery for the submit button
	$( "#btn_submit" ).button();
	
	
	//jquery for the textarea 
	$( "#call_description" ).keyup(function(){
		var max = 255;
		var len = $(this).val().length;
		if(len > max){
			$(this).val($(this).val().substring(0, 255));
		} else {
			$( "#char_counter" ).text(max - len);
		}
		
	});
	
	
	$("#btn_login").click (function(e) {
		$( "#call_form" ).submit(function(){
						
		});
	});
	
	
	
	
	//form validator function
	
	function id2Index(tabsId, srcId){
		var index = -1;
		var tbH = $(tabsId).find("li a");
		if(tbH.length > 0){
			var i = 0;
			for(i=0; i < tbH.length; i++){
				o=tbH[i];
				if(o.href.search(srcId)>0){
					index = i;
				}
			}
		}
		return index;
	}


	
	$( "#call_form" ).validate({
		errorPlacement: function(error, element) {
			alert(error[0].innerHTML);
		},
		/*
		messages: {
			call_type: {
				required: "Selecione o tipo de chamado."
			},
			client_field: {
				required: "Informe o Cliente."
			},
			product_field: {
				required: "Informe o Produto."
			},
			dt_field_1: {
				required: "Insira o código completo do Dispositivo de Teste.",
			},
			call_description: {
				required: "Insira a descrição do chamado."
			}
		},
		
		*/
		submitHandler: function( form ) {
			var call_type = $( "#call_type" ).val();
			var client = $( "#client_id" ).val();
			var product = $( "#product_id" ).val();
			var call_description = $( "#call_description" ).val().length;
			
			if( !call_type ) {
				alert("Selecione um tipo de chamado!");
				return(false);
			}
			if( !client ) {
				alert("Informe o Cliente!");
				return(false);
			}
			if ( !product ){
				alert("Informe o Produto!");
				return(false);
			}
			if( !call_description ){
				alert("Insira a descrição do chamado!");
				return(false);
			}
			
			
			var dados = $(form).serialize();
			
			$.ajax ({
				type: 		"POST",
				url: 		"code/call_form_process.php",
				data: 		dados,
				dataType:	"json",
				contentType: "application/x-www-form-urlencoded;charset=ISO-8859-15",	
				success:	function( data ){
					if(data.success == true){
						alert("Chamado criado com sucesso!");
						$("#tabs").tabs("option", "active", id2Index("#tabs", "#meus_chamados"));
						//window.location.href = "";
					} else {
						if(data.error == 1) {
							alert("Já existe um chamado aberto para o cliente e produto selecionados.");
						} else if(data.error == 2) {
							alert("Falha na criação do chamado. Entre em contato com a Engenharia de Testes e reporte o problema.");
						}
					}
				}
			});			
			
			return false;
		}
		
	});
	
	//Jquery for the tab "Meus Chamados"
	$( "#btn_backward" ).button();
	$( "#btn_forward" ).button();
	
	//global variable that contains the pages contained in "Meus Chamados"
	var num_page;
	var total_pages;
	var was_filtered = 0;
	
	function verifyStatusCall(status_call, date_call){

		switch(status_call) {
			case '1':
				var split_date = date_call.split(" ")[0].split("/");
				var split_hour = date_call.split(" ")[1].split(":");
				
				var date_hour_call = new Date(split_date[2], (split_date[1]-1), split_date[0], split_hour[0], split_hour[1], 0, 0);
				var date_hour_now = new Date();
				
				var date_hour_call_minutes = Math.floor((date_hour_call.getTime() / 1000) / 60);
				var date_hour_now_minutes = Math.floor((date_hour_now.getTime() / 1000) / 60);
				
				if((date_hour_now_minutes - date_hour_call_minutes) > 15) {
						return "table_wrapper_late";
				} else {
					return "table_wrapper_opened";
				}				
				break;
			case '2':
				return "table_wrapper_assisting";
				break;
			case '3':
				return "table_wrapper_closed";
			
		}
	}

	function loadCallPage(page, filter_opt, filter_value){
		var filter_data = "";
		if(filter_opt) {
			filter_data = "filter="+filter_opt+"&value="+encodeURI(filter_value);
		}	
		$.ajax({
			type: 		"POST",
			url: 		"code/my_calls_content.php",
			data: 		filter_data+"&page="+page+"&user="+Cookies.get("user"),
			dataType:	"json",
			success:	function( data ) {
				$( "#my_calls_wrapper" ).html("");
				$.each(data, function(key, value) {
					if(typeof value.cha_fechamento === "undefined"){
						value.cha_fechamento = "";
					}
					
					var style_call = verifyStatusCall(value.cha_status, value.cha_abertura); 
					
					var answer = "";
					if(value.cha_status == '3'){
						var answer = 
						"	<div class='my_call_table'>"																						+
						"		<div class='my_call_row'>"																						+	
						"			<div class='my_call_cell' id='label_col_call' name='label_col_call'>Atendido por :</div>"	         		+
						"			<div class='my_call_cell' id='content_col_call' name='content_col_call'>"+value.col_nome+"</div>"           +
						"		    <div class='my_call_cell' id='label_answer_call' name='label_answer_call'>Resposta: </div>"																	+
						"			<div class='my_call_cell' id='content_answer_call' name='content_answer_call'>"+value.ach_descricao+"</div>"+
						"		</div>"																											+
						"	</div>";	
							
					}
					
					var div = 
					"<div class='table_wrapper "+style_call+"'>"																			+
					"	<div class='my_call_table'>"	 																					+
					"		<div class='my_call_row'>"	 																					+
					"			<div class='my_call_cell' id='label_type_call' name='label_type_call'>Tipo: </div>"							+
					"			<div class='my_call_cell' id='content_type_call' name='content_type_call'>"+value.tch_descricao+"</div>"	+
					"			<div class='my_call_cell' id='label_client_call' name='label_client_call'>Cliente:</div>" 					+
					"			<div class='my_call_cell' id='content_client_call' name='content_client_call'>"+value.cli_nome+"</div>"		+		
					"			<div class='my_call_cell' id='label_product_call' name='label_product_call'>Produto:</div>" 				+
					"			<div class='my_call_cell' id='content_product_call' name='content_product_call'>"+value.pro_nome+"</div>" 														+
					"			<div class='my_call_cell' id='label_creator_call' name='label_creator_call'>Criado Por:</div>" 				+
					"			<div class='my_call_cell'>"+value.cha_operador+"</div>"																														+
					"		</div>"																											+
					"		<div class='my_call_row'>" 																						+
					"			<div class='my_call_cell' id='label_dt_call' name='label_dt_call'>Jiga:</div>"								+
					"			<div class='my_call_cell'>DT"+value.cha_DT+"</div>" 														+
					"			<div class='my_call_cell' id='label_opening_call' name='label_opening_call'>Abertura:</div>" 				+
					"			<div class='my_call_cell'>"+value.cha_abertura+"</div>"														+
					"			<div class='my_call_cell' id='label_close_call' name='label_close_call'>Fechamento:</div>"					+
					"			<div class='my_call_cell'>"+value.cha_termino+"</div>"														+
					"		</div>"																											+
					"	</div>"																												+
					"	<div class='my_call_table'>"																						+
					"		<div class='my_call_row'>"																						+	
					"			<div class='my_call_cell' id='label_desc_call' name='label_desc_call'>Descrição do Chamado:</div>"			+
					"		</div>"																											+
					"	</div>"																												+
					"	<div class='my_call_table'>"																						+
					"		<div class='my_call_row'>"																						+	
					"			<div class='my_call_cell' id='content_desc_call' name='content_desc_call'>"+value.cha_descricao+"</div>"	+
					"		</div>"																											+
					"	</div>"																												+
					answer+
					"</div>"																												+
					"<br>";
					
					$( "#my_calls_wrapper" ).append(div);
				});
				
			}
		});
	}
	
	//ajax call to update de paging of the tab "Meus Chamados"
	function loadPagesMyCalls(filter_opt, filter_value){
		var filter_data = "";
		if(filter_opt) {
			filter_data = "filter="+filter_opt+"&value="+encodeURI(filter_value);
		} 
	
		$.ajax({
			type: 		"POST",
			url: 		"code/my_calls_paging.php",
			data: 		filter_data+"&user="+Cookies.get("user"),
			dataType:	"json",
			success:	function( data ) {
				num_page = data.page;
				total_pages = data.total;
				$( "#btn_backward" ).button( "enable" );
				$( "#btn_forward" ).button( "enable" );
				if(data.page <= 1){
					$( "#btn_backward" ).button( "disable" );
				}
				if(data.page >= data.total){
					$( "#btn_forward" ).button( "disable" );
				}
				$( "#paging_value" ).html("<strong>"+data.page+" / "+data.total+"</strong>");
				
				loadCallPage(1, filter_opt, filter_value);
			}
		});
	}
	
	loadPagesMyCalls("", "");	
	
	
	
	//Jquery used for changing the pages of "Meus Chamados"
	$("#btn_forward").click (function(e) {
		num_page = num_page + 1;
		$( "#paging_value" ).html("<strong>"+ num_page +" / "+ total_pages +"</strong>");
		
		//load the data for the calls
		if(was_filtered){
			switch($( "#filter_options" ).val()){
				case "type":
					if($( "#type_value_filter" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#type_value_filter" ).val());			
					}
				break;
				case "client":
					if($( "#client_value_filter" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#client_value_filter" ).val());							
					}
				break;
				case "product":
					if($( "#product_value_filter" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#product_value_filter" ).val());							
					}
				break;
				case "jig":
					if($( "#dt_field_filter_1" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#dt_field_filter_1" ).val());						
					}
				break;
				case "opendate":
					if($( "#opendate_field_filter" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#opendate_field_filter" ).val());							
					}
				break;
				case "closedate":
					if($( "#closedate_field_filter" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#closedate_field_filter" ).val());					
					}
				break;
				case "status":
					if($( "#status_value_filter" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#status_value_filter" ).val());				
					}
				break;
				default:
					loadCallPage(num_page, "", "");
				break;
			}
		} else {
			loadCallPage(num_page, "", "");
		}
		
		//loadCallPage(num_page);
		
		
		$( "#btn_backward" ).button( "enable" );
		
		if(num_page >= total_pages){
			$( "#btn_forward" ).button( "disable" );
		}
	});
	
	$("#btn_backward").click (function(e) {
		num_page = num_page - 1;
		$( "#paging_value" ).html("<strong>"+ num_page +" / "+ total_pages +"</strong>");
		
		if(was_filtered){	
			switch($( "#filter_options" ).val()){
				case "type":
					if($( "#type_value_filter" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#type_value_filter" ).val());			
					}
				break;
				case "client":
					if($( "#client_value_filter" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#client_value_filter" ).val());							
					}
				break;
				case "product":
					if($( "#product_value_filter" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#product_value_filter" ).val());							
					}
				break;
				case "jig":
					if($( "#dt_field_filter_1" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#dt_field_filter_1" ).val());						
					}
				break;
				case "opendate":
					if($( "#opendate_field_filter" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#opendate_field_filter" ).val());							
					}
				break;
				case "closedate":
					if($( "#closedate_field_filter" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#closedate_field_filter" ).val());					
					}
				break;
				case "status":
					if($( "#status_value_filter" ).val()){
						loadCallPage(num_page, $( "#filter_options" ).val(), $( "#status_value_filter" ).val());				
					}
				break;
				default:
					loadCallPage(num_page, "", "");
				break;
			}
		} else {
			loadCallPage(num_page, "", "");
		}
		
	
		$( "#btn_forward" ).button( "enable" );
		
		if(num_page <= 1){
			$( "#btn_backward" ).button( "disable" );
		}
	});
	
	
	//jquery for the filter
	$( "#filter_options" ).selectmenu({
		style: 		'dropdown',
		width:		180,
		change:		function(event, ui){
			$( ".value_field_filter" ).hide();
			$( ".white_space_div" ).hide();
			if(ui.item.value == "type"){
				$( "#type_field_filter" ).show();
				$( "#wrapper_button_filter" ).show();
	
			} else if(ui.item.value == "client"){
				$( "#client_field_filter" ).show();
				$( "#wrapper_button_filter" ).show();
		
			} else if(ui.item.value == "product"){
				$( "#product_field_filter" ).show();
				$( "#wrapper_button_filter" ).show();
			
			} else if(ui.item.value == "jig"){
				$( "#jig_field_filter" ).show();
				$( "#wrapper_button_filter" ).show();

			} else if(ui.item.value == "opendate"){
				$( "#open_field_filter" ).show();
				$( "#wrapper_button_filter" ).show();

			} else if(ui.item.value == "closedate"){
				$( "#close_field_filter" ).show();
				$( "#wrapper_button_filter" ).show();
				
			} else if(ui.item.value == "status"){
				$( "#status_field_filter" ).show();
				$( "#wrapper_button_filter" ).show();
			} else {
				was_filtered = 0;
				loadPagesMyCalls("", "");
				$( "#wrapper_button_filter" ).hide();
			}
		}
	});
	

	//jquery for the call type filter field
	$( "#type_value_filter" ).selectmenu( {
		style: 'dropdown',
		width:		180		
	});
	
	//ajax call to populate de selectmenu with the call types
	$.ajax({
		type: 		"POST",
		url: 		"code/call_types_list.php",
		data: 		"",
		dataType:	"json",
		success:	function( data ) {
			$.each(data, function(index, calltype) {
				$("<option value='" + calltype.id + "'>" + calltype.descricao + "</option>").appendTo("#type_value_filter");
			});
		}
	});
	

	
//	Jquery for the DT fields in the filter, to prevent insertion of character
//jquery for the DT field code
	$( "#dt_field_filter_1" ).on("keypress keyup blur", function(event){
		$(this).val($(this).val().replace("/[^\d].+/", ""));
		
		if((event.which < 48) || (event.which > 57)){
			event.preventDefault();
		}
		
		var len = $(this).val().length;
		if(len >= 4){
			$( "#dt_field_filter_2" ).focus();
		}
	});
	
		
	//jquery for the Open Date datepicker field in the filter
	$( "#opendate_field_filter" ).datepicker({
		changeMonth:	true,
		changeYear:		true,
		showOn:			"button",
		buttonImage:	"css/images/calendar-icon.png",
		buttonText:		"Selecione uma data.",
		buttonImageOnly:	true
	});
		
	$( "#opendate_field_filter" ).datepicker( $.datepicker.regional[ "pt-BR" ] );
	
	//jquery for the Close Date datepicker field in the filter
	$( "#closedate_field_filter" ).datepicker({
		changeMonth:	true,
		changeYear:		true,
		showOn:			"button",
		buttonImage:	"css/images/calendar-icon.png",
		buttonText:		"Selecione uma data.",
		buttonImageOnly:	true
	});
		
	$( "#closedate_field_filter" ).datepicker( $.datepicker.regional[ "pt-BR" ] );
	
	
	//jquery for the call type filter field
	$( "#status_value_filter" ).selectmenu( {
		style: 'dropdown',
		width:		180		
	});
	
	//jquery for the Filter button
	
	$( "#btn_filter" ).button();
	
	$("#btn_filter").click (function(e) {
		was_filtered = 1;
		switch($( "#filter_options" ).val()){
			case "type":
				if($( "#type_value_filter" ).val()){
					loadPagesMyCalls($( "#filter_options" ).val(), $( "#type_value_filter" ).val());	
				}
			break;
			case "client":
				if($( "#client_value_filter" ).val()){
					loadPagesMyCalls($( "#filter_options" ).val(), $( "#client_value_filter" ).val());	
				}
			break;
			case "product":
				if($( "#product_value_filter" ).val()){
					loadPagesMyCalls($( "#filter_options" ).val(), $( "#product_value_filter" ).val());	
				}
			break;
			case "jig":
				if($( "#dt_field_filter_1" ).val()){
					loadPagesMyCalls($( "#filter_options" ).val(), $( "#dt_field_filter_1" ).val());	
				}
			break;
			case "opendate":
				if($( "#opendate_field_filter" ).val()){
					loadPagesMyCalls($( "#filter_options" ).val(), $( "#opendate_field_filter" ).val());	
				}
			break;
			case "closedate":
				if($( "#closedate_field_filter" ).val()){
					loadPagesMyCalls($( "#filter_options" ).val(), $( "#closedate_field_filter" ).val());	
				}
			break;
			case "status":
				if($( "#status_value_filter" ).val()){
					loadPagesMyCalls($( "#filter_options" ).val(), $( "#status_value_filter" ).val());	
				}
			break;
		}
	});
	//JQuery for the report screeen
	
	var today = new Date();
	var yesterday = new Date();
	yesterday.setDate(today.getDate() - 1);
	
	
	//jquery for the Open Date datepicker field in the Report filter
	$( "#opendate_field_report_filter" ).datepicker({
		changeMonth:	true,
		changeYear:		true,
		showOn:			"button",
		buttonImage:	"css/images/calendar-icon.png",
		buttonText:		"Selecione uma data.",
		buttonImageOnly:	true
	});
		
	$( "#opendate_field_report_filter" ).datepicker( $.datepicker.regional[ "pt-BR" ] );
	
	$( "#opendate_field_report_filter" ).datepicker().datepicker("setDate", yesterday);
	
	//jquery for the Close Date datepicker field in the Report filter
	$( "#closedate_field_report_filter" ).datepicker({
		changeMonth:	true,
		changeYear:		true,
		showOn:			"button",
		buttonImage:	"css/images/calendar-icon.png",
		buttonText:		"Selecione uma data.",
		buttonImageOnly:	true
	});
		
	$( "#closedate_field_report_filter" ).datepicker( $.datepicker.regional[ "pt-BR" ] );
	
	$( "#closedate_field_report_filter" ).datepicker().datepicker("setDate", today);
	
	//JQuery for the report filter buttons
	$( "#btn_report_filter_clear" ).button();
	
	$( "#btn_report_filter" ).button();
	
	$( "#btn_report_filter_save" ).button();
	
	//Code to fix the report table header width
	function resizeReportTableElements() {
		var $table = $( "#report_calls_table" );
		var $bodyCells = $table.find("thead tr:first").children();
		var colWidth;
		
		colWidth = $bodyCells.map(function(){
			return $(this).width();
		}).get();
		
		
		$table.children('tbody').find('tr').each(function(i, v){
			$(v).children().each(function(j, e){
				$(e).width(colWidth[j]);
			});
		});
		
	}
	
	
	//Function created to insert an element to the last line of the report table
	function appendToReportTable(rowData){
		var lastRow = $('<tr/>').appendTo($('#report_calls_table').find('tbody:last'));
		$.each(rowData, function(colIndex, c){
			lastRow.append($('<td\>').html(c));
		});
	}
	
	//function created to fillup with zeros integers at left
	function zeroPad(num, places) {
		var zero = places - num.toString().length + 1;
		return Array(+(zero > 0 && zero)).join("0") + num;
	}
	
	//function created to format the time to a string in the XX:XX format
	function formatTime(minutes){
		var positive = 1;
		if(minutes < 0) {
			positive = 0;
		}
		var hours = Math.floor(Math.abs(minutes)/60);
		var remainder = Math.abs(minutes) % 60;
		if(minutes > 30 && positive) {
			return "<font color='red'>"+zeroPad(hours, 2)+":"+zeroPad(remainder, 2)+"</font>";	
		} else  if(positive == 0){
			return "<font color='blue'>-"+zeroPad(hours, 2)+":"+zeroPad(remainder, 2)+"</font>"
		} else {
			return zeroPad(hours, 2)+":"+zeroPad(remainder, 2);	
		}
	}
	
	//calculate the Call Wait Time
	function calculateWaitTime(call_status, total_duration, attendance_time){
		if(Number(call_status) == 1){
			return total_duration;
		} else {
			return total_duration - attendance_time;
		}
	}

	
	//Ajax code used to populate the report table
	$.ajax({
		type: 		"POST",
		url: 		"code/report_calls_content.php",
		data: 		"",
		dataType:	"json",
		success:	function( data ) {
			$( "#body_report_table" ).empty();
			$.each(data, function(index, callData) {
				var tableDataRow = [
					callData.cha_data_hora_abertura, 
					formatTime(calculateWaitTime(callData.cha_status, callData.duracao_total, callData.duracao_atendimento)), 
					(callData.cha_status > 1 ? formatTime(callData.duracao_atendimento) : ""),
					formatTime(callData.duracao_total),
					callData.cha_operador,
					callData.cli_nome,
					callData.pro_nome,
					callData.col_nome,
					callData.ach_descricao
				];
				appendToReportTable(tableDataRow);
			});
			resizeReportTableElements();
		}
	});
			
	
	$( "#btn_report_filter" ).click(function(e){
		
		$("#report_filtering").val("1");
		
		var begin_date = $( "#opendate_field_report_filter" ).val();
		var end_date = $( "#closedate_field_report_filter" ).val();
		var client = $( "#client_value_report_filter" ).val();
		var product = $( "#product_value_report_filter" ).val();
		var creator = $( "#creator_value_report_filter" ).val();
		var support = $( "#responsible_value_report_filter" ).val();
		
		var filterData = "filter=1&begin_date="+begin_date+"&end_date="+end_date+"&client="+client+"&product="+product+"&creator="+creator+"&support="+support;
		$.ajax({
			type: 		"POST",
			url: 		"code/report_calls_content.php",
			data: 		filterData,
			dataType:	"json",
			success:	function( data ) {
				$( "#body_report_table" ).empty();
				$.each(data, function(index, callData) {
					var tableDataRow = [
						callData.cha_data_hora_abertura, 
						formatTime(calculateWaitTime(callData.cha_status, callData.duracao_total, callData.duracao_atendimento)), 
						(callData.cha_status > 1 ? formatTime(callData.duracao_atendimento) : ""),
						formatTime(callData.duracao_total),
						callData.cha_operador,
						callData.cli_nome,
						callData.pro_nome,
						callData.col_nome,
						callData.ach_descricao
					];
					appendToReportTable(tableDataRow);
				});
				resizeReportTableElements();
			}
		});
	});
	
	$( "#btn_report_filter_clear" ).click(function(e){
		
		var today = new Date();
		var yesterday = new Date();
		yesterday.setDate(today.getDate() - 1);
		
		$("#report_filtering").val("0");
		
		$( "#opendate_field_report_filter" ).val(zeroPad(yesterday.getDate(), 2)+"/"+zeroPad((yesterday.getMonth()+1), 2)+"/"+yesterday.getFullYear());
		$( "#closedate_field_report_filter" ).val(zeroPad(today.getDate(), 2)+"/"+zeroPad((today.getMonth()+1), 2)+"/"+today.getFullYear());
		$( "#client_value_report_filter" ).val("");
		$( "#product_value_report_filter" ).val("");
		$( "#creator_value_report_filter" ).val("");
		$( "#responsible_value_report_filter" ).val("");
		
		
		$.ajax({
			type: 		"POST",
			url: 		"code/report_calls_content.php",
			data: 		"",
			dataType:	"json",
			success:	function( data ) {
				$( "#body_report_table" ).empty();
				$.each(data, function(index, callData) {
					var tableDataRow = [
						callData.cha_data_hora_abertura, 
						formatTime(calculateWaitTime(callData.cha_status, callData.duracao_total, callData.duracao_atendimento)), 
						(callData.cha_status > 1 ? formatTime(callData.duracao_atendimento) : ""),
						callData.cha_operador,
						callData.cli_nome,
						callData.pro_nome,
						callData.col_nome,
						callData.dtr_descricao
					];
					appendToReportTable(tableDataRow);
				});
				resizeReportTableElements();
			}
		});		
		
	});
	
	$( "#btn_report_filter_save" ).click(function(e){
		
		if($("#report_filtering").val()){
			var begin_date = $( "#opendate_field_report_filter" ).val();
			var end_date = $( "#closedate_field_report_filter" ).val();
			var client = $( "#client_value_report_filter" ).val();
			var product = $( "#product_value_report_filter" ).val();
			var creator = $( "#creator_value_report_filter" ).val();
			var support = $( "#responsible_value_report_filter" ).val();
			
			var filterData = "filter=1&begin_date="+begin_date+"&end_date="+end_date+"&client="+client+"&product="+product+"&creator="+creator+"&support="+support;
		} else {
			var filterData = "";
		}
		
		$.ajax({
			type: 		"POST",
			url: 		"code/spreadsheet_generator.php",
			data: 		filterData,
			dataType:	"json",
			complete:	function( data ) {
				window.location.href = 'Relatorio.xls';
			}
		});
	});
});	
