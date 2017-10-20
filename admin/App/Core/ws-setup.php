<?php
	############################################################################################################################################
	# ESTE É PRATICAMENTE O 1° ARQUIVO A SER ABERTO NO SISTEMA, QUE É A TELA DE INSTALAÇÃO
	############################################################################################################################################
	#
	#			SIM, AINDA NÃO ESTÁ EM MVC!!!
	#			Em breve cuidarei disso!
	#
	############################################################################################################################################
	# CAPTAMOS A BASE DO SISTEMA
	############################################################################################################################################
		$path = basename(realpath(__DIR__ . '/../'));
	############################################################################################################################################
	# GERA UMA SENHA PARA OS CAMPOS DO ARQUIVO DE CONFIGURAÇÃO
	############################################################################################################################################
		function generatePassword($length = 8) {
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			$count = mb_strlen($chars);
			for ($i = 0, $result = ''; $i < $length; $i++) {
				$index = rand(0, $count - 1);
				$result .= mb_substr($chars, $index, 1);
			}
			return $result;
		}

	############################################################################################################################################
	# COPIA UM DIRETÓRIO INTEIRO PARA OUTRO LOCAL
	############################################################################################################################################
		function CopiaDir($DirFont, $DirDest) {
			if (!file_exists($DirDest)) {
				mkdir($DirDest);
			}
			if ($dd = opendir($DirFont)) {
				while (false !== ($Arq = readdir($dd))) {
					if ($Arq != "." && $Arq != "..") {
						$PathIn  = "$DirFont/$Arq";
						$PathOut = "$DirDest/$Arq";
						if (is_dir($PathIn)) {
							CopiaDir($PathIn, $PathOut);
						} elseif (is_file($PathIn)) {
							copy($PathIn, $PathOut);
						}
					}
				}
				closedir($dd);
			}
		}

	############################################################################################################################################
	# CASO ESTE ARQUIVO SEJA INVOCADO COM A FUNÇÃO DE INSTALAÇÃO EXECUTA
	############################################################################################################################################
		if (isset($_POST['function']) && $_POST['function'] == "createWsConfig") {

			############################################################################################################################################
			# TRANSFORMA O POST DO FORMULARIO EM ARRAY
			############################################################################################################################################
			parse_str($_POST['form'], $data);

			############################################################################################################################################
			# SEPARA AS VARIÁVEIS PARA GRAVAÇÃO DO ARQUIVO
			############################################################################################################################################
			$isso          = Array(
				"{DOMINIO}",
				"{PREFIX_TABLES}",
				"{NOME_BD}",
				"{USUARIO_BD}",
				"{SENHA_BD}",
				"{SERVIDOR_BD}",
				"{ROOT_WEBSITE}",
				"{ROOT_ADMIN}",
				"{RECAPTCHA}",
				"{LANG}",
				"{ROOT_DOCUMENT}"
			);
			$porisso       = Array(
				str_replace(PHP_EOL, "", $data['DOMINIO']),
				str_replace(PHP_EOL, "", $data['PREFIX_TABLES']),
				str_replace(PHP_EOL, "", $data['NOME_BD']),
				str_replace(PHP_EOL, "", $data['USUARIO_BD']),
				str_replace(PHP_EOL, "", $data['SENHA_BD']),
				str_replace(PHP_EOL, "", $data['SERVIDOR_BD']),
				$data['ROOT_DOCUMENT'] . '/website',
				$data['ROOT_DOCUMENT'] . '/admin',
				str_replace(PHP_EOL, "", $data['RECAPTCHA']),
				$data['LANG'],
				$data['ROOT_DOCUMENT']
			);
			$isso_Password = array(
				'{ID_SESS}',
				'{NAME_SESS}',
				'{TOKEN_DOMAIN}',
				'{TOKEN_ACCESS}',
				'{TOKEN_USER}',
				'{AUTH_KEY}',
				'{SECURE_AUTH_KEY}',
				'{LOGGED_IN_KEY}',
				'{NONCE_KEY}',
				'{AUTH_SALT}',
				'{SECURE_AUTH_SALT}',
				'{LOGGED_IN_SALT}',
				'{NONCE_SALT}'
			);
			$ashTokens     = Array();

			############################################################################################################################################
			# GERA OS TOKENS DE ACESSO DO CONFIG
			############################################################################################################################################
			foreach ($isso_Password as $value) {
				$ashTokens[] = generatePassword(rand(15, 20));
			}

			##########################################################################################################################################
			# PEGAMOS A STRING O ARQUIVO CONFIG BASE
			##########################################################################################################################################
			$wsConfigDefault         = file_get_contents($data['ROOT_DOCUMENT'].'/admin/App/Config/ws-config-default.php');

			##########################################################################################################################################
			# FORMATAMOS O CONTEUDO DELE
			##########################################################################################################################################
			$wsConfigDefaultFormated = str_replace($isso, $porisso, $wsConfigDefault);
			$wsConfigDefaultFormated = str_replace($isso_Password, $ashTokens, $wsConfigDefaultFormated);

			#######################################################################################################################################
			# GRAVAMOS O NOVO ARQUIVO ws-config.php E RETORNAMOS O RESULTADO
			#######################################################################################################################################
			if (!file_put_contents($data['ROOT_DOCUMENT'] . '/ws-config.php', $wsConfigDefaultFormated)) {
				echo json_encode(array(
					'status' => 'falha',
					'resposta' => 'Não foi possível gravar o ws-config.php'
				));
			} else {
				echo json_encode(array(
					'status' => 'sucesso',
					'resposta' => 'ws-config.php criado com sucesso!'
				));
			}
			exit;
		}

	############################################################################################################################################
	# CASO ESTE ARQUIVO SEJA INVOCADO COM A FUNÇÃO DE VALIDAÇÃO DE CONEXÃO DO MYSQL
	############################################################################################################################################
		if (isset($_POST['function']) && $_POST['function'] == "testMySQL") {
			@mysqli_connect($_POST['SERVIDOR_BD'], $_POST['USUARIO_BD'], $_POST['SENHA_BD'], $_POST['NOME_BD']);
			if (mysqli_connect_errno()) {
				echo "0";
			} else {
				echo "1";
			}
			exit;
		}

	############################################################################################################################################
	# COPIAMOS O HTACCES PADRÃO DO SISTEMA PARA O CAMINHO ROOT
	############################################################################################################################################
		copy(ROOT_DOCUMENT.'/admin/App/Templates/txt/ws-first-htaccess.txt', ROOT_DOCUMENT . '/.htaccess');

	############################################################################################################################################
	# CRIAMOS TODOS OS DIRETORIOS DO WEBSITE A SER MONTADO
	############################################################################################################################################
	
		@mkdir(ROOT_DOCUMENT 				.'/website');
		@mkdir(ROOT_DOCUMENT 				.'/ws-update');
		@mkdir(ROOT_DOCUMENT 				.'/ws-bkp');
		@mkdir(ROOT_DOCUMENT 				.'/ws-cache');
		@mkdir(ROOT_DOCUMENT 				.'/website/includes');
		@mkdir(ROOT_DOCUMENT 				.'/website/plugins');
		CopiaDir(ROOT_DOCUMENT 				.'/admin/App/Modulos/plugins', ROOT_DOCUMENT . '/website/plugins');
		@mkdir(ROOT_DOCUMENT 				.'/website/assets');
		@mkdir(ROOT_DOCUMENT 				.'/website/assets/upload-files');
		@mkdir(ROOT_DOCUMENT 				.'/website/assets/upload-files/thumbnail');
		@mkdir(ROOT_DOCUMENT 				.'/website/assets/libraries');
		@mkdir(ROOT_DOCUMENT 				.'/website/assets/css');
		@mkdir(ROOT_DOCUMENT 				.'/website/assets/js');
		@mkdir(ROOT_DOCUMENT 				.'/website/assets/img');
		@mkdir(ROOT_DOCUMENT 				.'/website/assets/template');
		@mkdir(ROOT_DOCUMENT 				.'/website/assets/fonts');
		@file_put_contents(ROOT_DOCUMENT 	.'/website/assets/.htaccess', 'RewriteEngine Off');
		@mkdir(ROOT_DOCUMENT 				.'/ws-shortcodes');
		@copy(ROOT_DOCUMENT."/admin/App/Lib/my-shortcode.php",ROOT_DOCUMENT."/ws-shortcodes/my-shortcode.php");
		
		if (!file_exists(ROOT_DOCUMENT 		.'/website/includes/header.php')) 	@file_put_contents(ROOT_DOCUMENT . '/website/includes/header.php', 'Header<hr>');
		if (!file_exists(ROOT_DOCUMENT 		.'/website/includes/erro404.php')) 	@file_put_contents(ROOT_DOCUMENT . '/website/includes/erro404.php', 'ERRO 404!');
		if (!file_exists(ROOT_DOCUMENT 		.'/website/includes/inicio.php')) 	@file_put_contents(ROOT_DOCUMENT . '/website/includes/inicio.php', 'Olá mundo!');
		if (!file_exists(ROOT_DOCUMENT 		.'/website/includes/footer.php')) 	@file_put_contents(ROOT_DOCUMENT . '/website/includes/footer.php', '<hr>Footer');
		if (file_exists(ROOT_DOCUMENT 		.'/website/index.php')) 			@rename(ROOT_DOCUMENT . '/website/index.php', ROOT_DOCUMENT . '/website/index_bkp.php');


	############################################################################################################################################
	# SIMPLES FUNÇÃO QUE RETORNA UMA SENHA CRYPT COM MD5
	############################################################################################################################################
		function _crypt() {
			$CodeCru        = @crypt(md5(rand(0, 50)));
			$vowels         = array("$", "/", ".", '=');
			$onlyconsonants = str_replace($vowels, "", $CodeCru);
			return substr($onlyconsonants, 1);
		}
?>
<html lang="pt-br" class='bgradial01' id="html">
<head>
<meta charset="UTF-8">
<link type="image/x-icon" href="./img/favicon.png" rel="shortcut icon" />
<meta name="viewport" content="width=device-width, initial-scale=1">

<link	type="text/css" media="all"		rel="stylesheet"						href="./App/Templates/css/websheep/global.css" />
<link	type="text/css" media="all"		rel="stylesheet" 						href="./App/Templates/css/websheep/estrutura.min.css" />
<link	type="text/css" media="all"		rel="stylesheet" 						href="./App/Templates/css/websheep/desktop.min.css" />
<link	type="text/css" media="all"		rel="stylesheet"						href="./App/Templates/css/websheep/install.css" />
<link	type="text/css" media="all"		rel="stylesheet"						href="./App/Templates/css/websheep/funcionalidades.css" />
<link	type="text/css" media="all"		rel="stylesheet" 						href="./App/Templates/css/fontes/fonts.css" />
<link	type="text/css" media="all"		rel="stylesheet"						href="./App/Templates/css/websheep/theme_blue.min.css" />
<script type = 'text/javascript' 												src="./App/Vendor/jquery/2.2.0/jquery.min.js"></script>
<script type = 'text/javascript' 												src="./App/Templates/js/websheep/funcionalidades.js"></script>



<script type = 'text/javascript'>
$(document).ready(function(){
	$("input[readonly]").css({color:"#CCC"});
	$("input[data-conect='mysql']").on("keyup",function(){
		var NOME_BD 		=	$("input[name='NOME_BD']").val();
		var USUARIO_BD 		=	$("input[name='USUARIO_BD']").val();
		var SENHA_BD 		=	$("input[name='SENHA_BD']").val();
		var SERVIDOR_BD 	=	$("input[name='SERVIDOR_BD']").val();
		$.ajax({
			type: "POST",cache: false,url: "./App/Core/ws-setup.php",
			data: {function:"testMySQL",NOME_BD:NOME_BD,USUARIO_BD:USUARIO_BD,SENHA_BD:SENHA_BD,SERVIDOR_BD:SERVIDOR_BD,},
			error: function (xhr, ajaxOptions, thrownError) {alert(xhr.status);alert(thrownError);}
		}).done(function(data) { 
			
			if(data=='1'){
				$("input[name='NOME_BD'],input[name='USUARIO_BD'],input[name='SENHA_BD'],input[name='SERVIDOR_BD']").css({borderColor:"#b0d000",paddingLeft:33,'background-image':"url('./App/Templates/img/websheep/tick-circle.png')",'background-position':10,'background-repeat':"no-repeat"})
			}else{
				$("input[name='NOME_BD'],input[name='USUARIO_BD'],input[name='SENHA_BD'],input[name='SERVIDOR_BD']").css({borderColor:"#d03b00",paddingLeft:33,'background-image':"url('./App/Templates/img/websheep/cross.png')",'background-position':10,'background-repeat':"no-repeat"})
			}
		});
	})
	window.criaWSConf = function(){
		var formulario = $("#formulario").serialize();
		$.ajax({
			type: "POST",
			cache: false,
			url: "./App/Core/ws-setup.php",
		    beforeSend:function(){confirma({width:"auto",conteudo:"  Criando ws-config...<div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 53px;background-image:url(\"./App/Templates/img/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>",drag:false,bot1:0,bot2:0})},
			data: {function:"createWsConfig", form:formulario},
		}).done(function(data) {
					objJSON = JSON.parse(data)
					console.log(data);
					if(objJSON.status=="sucesso"){
						$.ajax({
								type: "POST",
								cache: false,
								beforeSend:function(){confirma({width:"auto",conteudo:"  Configurando MySQL...<div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 53px;background-image:url(\"./App/Templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>",drag:false,bot1:0,bot2:0})},
								url: "./App/Modulos/_tools_/functions.php",
								data: {function:"installSQLInit",formulario:formulario},
								error: function (xhr, ajaxOptions, thrownError) {alert(xhr.status);alert(thrownError);}
							}).done(function(data) {
								console.log(data);
								if(data=="sucesso"){
									location.reload();
								}else{
									confirma({
										conteudo:data,
										bot1:'Ok',
										bot2:false,
										drag:false,
										botclose:false,
										width:500
									})
								}
							});

					}else{
				}
		})
	}

	$('#vamosla').click(function(){
			var formulario 		= 	$("#formulario").serialize();
			var NOME_BD 		=	$("input[name='NOME_BD']").val();
			var USUARIO_BD 		=	$("input[name='USUARIO_BD']").val();
			var SENHA_BD 		=	$("input[name='SENHA_BD']").val();
			var SERVIDOR_BD 	=	$("input[name='SERVIDOR_BD']").val();
			var CLIENT_NAME 	=	$("input[name='CLIENT_NAME']").val();
			window.criaWSConf();
	});
});
</script>

<body id="body">
	<div id='avisoTopo'></div>
	<div id="container" style="width: 100%; left: 0; position: fixed; top: 0; height: 100%; overflow: auto;"> 
	<div id="conteudo">
		<div class="w1" style="border:solid 1px #CCC;position: relative;transform: translate(-50%,0);left: 50%;padding: 30px;width: 800px;float: left;top: 10px;">
			<div id='step0' style="position: relative;float: left;text-align: center;">
				<strong style="font-family: 'Titillium Web', sans-serif;font-size: 30px;line-height">Bem-Vindo(a) ao WebSheep!</strong><br>
				Notamos que você ainda nao tem um arquivo <strong>ws-config.php</strong><br>
				Nele irá todos os dados do servidor e banco de dados. preencha o formulário e clique em avançar.
				</p>
				<hr style="margin:20px;">
					<form id="formulario">
						<div class="label" style="width: 100%;">Nome do cliente licenciado:</div>
						<div class="c"></div>
						<input	name="CLIENT_NAME" 	value="" placeholder="ex: Empresa LTDA" style="width: 100%;">
						<div class="c"></div>
						<div class="label" style="width: 50%;">Login do webmaster:</div>
						<div class="label" style="width: 44%;">Senha do webmaster:</div>
						<input	name="LOG_WEBMASTER" 	value="admin" 						placeholder="" style="width: 50%;">
						<input	name="PASS_WEBMASTER" 	value="admin123" 	type="password" placeholder="" style="width: 47%;">
						<div class="c"></div>
						<div class="label">Nome do banco MySQL</div>
						<div class="label">Nome do usuário MySQL</div>
						<input	data-conect="mysql" name="NOME_BD" 					value="">
						<input	data-conect="mysql" name="USUARIO_BD" 				value="root">
						<div style="width: 244px;" class="label">Senha do MySQL</div>
						<div style="width: 244px;" class="label">Nome do servidor MySQL</div>
						<div style="width: 244px;" class="label">Prefixo das tabelas</div>
						<input	style="width: 255px;" data-conect="mysql" name="SENHA_BD"		type="password" value="">
						<input	style="width: 255px;" data-conect="mysql" name="SERVIDOR_BD"	value="localhost">
						<input	style="width: 255px;" name="PREFIX_TABLES"	value="">
						<input	type="hidden" name="DOMINIO" 				value="<?= $_SERVER['HTTP_HOST'] ?>">
						<input	type="hidden" name="DOMINIO_SEC" 			value="<?= $_SERVER['HTTP_HOST'] ?>">

						<div style="width: 47%;text-align:left;" class="label">Token do Recaptcha do Google</div>
						<div style="width: 47%;text-align:left;" class="label">Idioma do sistema</div>
						<input	name="RECAPTCHA" value="">
						<select name="LANG"><?
							$pasta = './App/Config/lang';
							if(is_dir($pasta)){
								$dh = opendir($pasta);
								while($diretorio = readdir($dh)){
									if($diretorio != '..' && $diretorio != '.'){
										$lang = str_replace('.json',"",$diretorio);
										echo '<option value="'.$lang.'">'.$lang.'</option>';
									}
								}
							}
							?></select>

						<input	type="hidden" name="TOKEN_ACCESS" 			value="<?= _crypt() ?>"	readonly>
						<input	type="hidden" name="TOKEN_USER" 			value="<?= _crypt() ?>"	readonly>
						<input	type="hidden" name="TOKEN_DOMAIN" 			value="<?= _crypt() ?>" 	readonly>
						<input	type="hidden" name="_PATCH_ADMIN_" 			value="<?= $path?>" 		readonly>
						<input	type="hidden" name="ID_SESS" 				value="<?= substr(_crypt(), 0, 6) ?>" readonly>
						<input	type="hidden" name="NAME_SESS" 				value="<?= substr(_crypt(), 0, 6) ?>" readonly>
						<input	type="hidden" name="ROOT_DOCUMENT" 			value="<?= ROOT_DOCUMENT?>" readonly>
					</form>
					<div class="c"></div>
					<div class="step botao vamosla" id="vamosla" style="width: 100%;">Criar ws-config.php e continuar a instalação</div>
			</div>
		</div>
	</div>
</div>
</body>