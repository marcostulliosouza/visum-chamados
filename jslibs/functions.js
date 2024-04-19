$( document ).ready(function() {
	Cookies.remove("user", {path: "./callsystem.php"});
	Cookies.remove("time", {path: "./callsystem.php"});

	//create a jQuery button
	$( "#btn_login" ).button();

	//add a css class to overwrite the jQuery css		
	$( "#btn_login" ).addClass('loginButton');

	//create an event that calls a php file that validates
	//the user/password using LDAP
	$("#btn_login").click (function(e) {
		e.preventDefault();
		$("#alert_wrong_user").css('display', 'none');
		$("#alert_empty_field").css('display', 'none');
		$("#alert_blocked_user").css('display', 'none');
		
		$.ajax({
			type:		"POST",
			url:		"code/validation.php",
			data:		{user: $("#username").val(), pass: $("#password").val() },
			dataType:	"json",
			success:	function(result){
				console.log(result);
				//if the user is valid create a cookie and redirect to the system page
				if(result.logged == '1')
				{
					var dt = new Date();
					var time = Math.floor(dt.getTime()/1000);
					Cookies.set("user", $("#username").val(), {path: "callsystem.php"});
					Cookies.set("time", time, {path: "callsystem.php"});
					window.location.href = "callsystem.php";
				}
				else if(result.logged == '0')
				{
					$("#alert_wrong_user").css('display', '');					
				}
				else if(result.logged == '-1')
				{
					$("#alert_empty_field").css('display', '');						
				}
				else if(result.logged == '-2')
				{
					$("#alert_blocked_user").css('display', '');						
				}
			},
			error:		function(result){
				console.log(result);
				$("#alert_wrong_user").css('display', '');
			}
		});
	});
	
	//Handler to enter keypress on password
	$("#password").keypress( function(event) {
		if(event.which == 13)
		{
			$("#btn_login").click();
		}	
		
	});
	
	//Handler to enter keypress on username
	$("#username").keypress( function(event) {
		if(event.which == 13)
		{
			$("#btn_login").click();
		}	
		
	});
	
	$("#username").focus();
	
	
});	
