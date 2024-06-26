<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="jqueryUI/jquery-ui.min.css">
    <link rel="stylesheet" href="css/extra-style.css">
    <script src="external/jquery/jquery.js"></script>
    <script src="jslibs/js.cookie.js"></script>
    <script src="jqueryUI/jquery-ui.min.js"></script>   
    
     <!-- Adicionando cabeçalhos de cache para evitar o armazenamento em cache da página de login -->
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="Fri, 14 Jun 1991 05:00:00 GMT">
</head>
<body>
    <div class="logo-hi-mix">
        <img src="/css/images/Logo-White.svg" height="100px" width="200px">
		<p>Chamados Engenharia de Teste</p>
	</div>
    <div>
    <div id="div_login" class="ui-widget ui-front ui-widget-content ui-corner-all ui-widget-shadow">
        <div class="login_fields">
            <div id="div_user">Usuário:</div><div id="user_field"><input id="username" size="25"></div>
        </div>
        <div class="login_fields">
            <div id="div_pass">Senha:</div><div id="pass_field"><input type="password" id="password" size="25"></div>
        </div>
        <div class="login_fields" id="div_btn">
            <button id="btn_login">Entrar</button>
        </div>
    </div>
    <div id="alert_empty_field" class="ui-widget" style="display: none">
        <div class="ui-state-error ui-corner-all">
            <p><span class="ui-icon ui-icon-alert"></span>
            <strong>Atenção:</strong> Campo de usuário ou senha vazio.</p>
        </div>
    </div>
    <div id="alert_wrong_user" class="ui-widget" style="display: none">
        <div class="ui-state-error ui-corner-all">
            <p><span class="ui-icon ui-icon-alert"></span>
            <strong>Atenção:</strong> Usuário ou senha incorreto.</p>
        </div>
    </div>
    <div id="alert_blocked_user" class="ui-widget" style="display: none">
        <div class="ui-state-error ui-corner-all">
            <p><span class="ui-icon ui-icon-alert"></span>
            <strong>Atenção:</strong> Usuário não possui permissão para abrir chamados.</p>
        </div>
    </div>
    </div>
    
    <script src="jslibs/functions.js"></script>
    
</body>
</html>
