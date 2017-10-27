<? 
	include_once(ROOT_DOCUMENT.'/ws-config.php');
	$php_version = file_get_contents(ROOT_ADMIN.'/App/Templates/txt/ws-php-version.txt');
	##########################################################################################
	#  VERSÃO DO SISTEMA   
	##########################################################################################
	$ws_version = json_decode(@file_get_contents(ROOT_ADMIN."/App/Templates/json/ws-update.json"));

	######################################################################################################################################
	#  QUANDO SE UTILIZA UM ARQUIVO, NÃO PODEMOS FAZER ELE SE AUTO EXCLUIR, PORTANTO    
	#  QUANDO FIZEMOS O INSTALL OU UPDATE, GRAVAMOS UMA CÓPIA NO ROOT, E AGORA SIM VAMOS SUBSTITUIR O WS-INSTALL (caso tenha)  
	######################################################################################################################################	
	if(file_exists(ROOT_DOCUMENT.'/ws-install.php')){
		unlink(ROOT_DOCUMENT.'/ws-install/ws-install.php');
		rename(ROOT_DOCUMENT.'/ws-install.php',ROOT_DOCUMENT.'/ws-install/ws-install.php');
	}

	######################################################################################################################################
	######################################################################################################################################

?>
<html lang="pt-br" class='bgradial01' id="html">
<head>
<meta charset="UTF-8">
<link type="image/x-icon" href="/admin/App/Templates/img/websheep/favicon.ico" rel="shortcut icon" />
<meta name="viewport" content="width=device-width, initial-scale=1">


<!-- <link	type="text/css" media="all"		rel="stylesheet"						href="./App/Templates/css/websheep/global.css" /> -->
<link	type="text/css" media="all"		rel="stylesheet" 						href="./App/Templates/css/websheep/estrutura.min.css" />
<link	type="text/css" media="all"		rel="stylesheet" 						href="./App/Templates/css/websheep/desktop.min.css" />
<link	type="text/css" media="all"		rel="stylesheet"						href="./App/Templates/css/websheep/install.css" />
<link	type="text/css" media="all"		rel="stylesheet"						href="./App/Templates/css/websheep/funcionalidades.css" />
<link	type="text/css" media="all"		rel="stylesheet" 						href="./App/Templates/css/fontes/fonts.css" />
<link	type="text/css" media="all"		rel="stylesheet"						href="./App/Templates/css/websheep/theme_blue.min.css?v=1" />
<script type = 'text/javascript' 												src="./App/Vendor/jquery/2.2.0/jquery.min.js"></script>
<script type = 'text/javascript' 												src="./App/Templates/js/websheep/funcionalidades.js"></script>


<script type = 'text/javascript'>
$(document).ready(function(){
	$("#eu_aceito").click(function(){if ( $(this).is(':checked') ) {$(this).val('1');}else{$(this).val('0');}})
	$('#vamosla').click(function(){
			window.finalizado=false;
		if($("#eu_aceito").val()=='1'){
			confirma({
				width:"auto",
				conteudo:"  Atualizando o sistema...<div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 68px;background-image:url(\"./App/Templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>",
				drag:false,
				bot1:0,
				bot2:0,
				divScroll: "body",
				divBlur: "#container",
				posFn:function(){
					$.ajax({
						type: "POST",
						cache: false,
						url: "./App/Modulos/_tools_/functions.php",
						data: {function:"installSQLInit"},
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						} 
					}).done(function(e) {
						console.log(e)
						if(e=="sucesso"){
							location.reload();
						}else{
							TopAlert({mensagem: e,type: 2});
						}
					});
				}
			})

		}else{
			TopAlert({mensagem: "Ops, esqueceu de aceitar o termo.\nLogo embaixo do texto =)",type: 2});
		}
	})
});
</script>
<body id="body" style="margin: 0px;">
	<div id='avisoTopo'></div>
	<div id="container">
			<div id="palco" class="w1" >
				<div id='step0' style="position: relative;float: left;text-align: center;">
					<div id="resposta"></div>
					<img src="./App/Templates/img/websheep/logo_ws_install.jpg" style="">
					<div class="c"></div>
					<strong style="font-family: 'Titillium Web', sans-serif;font-size: 30px;line-height;font-weight: 700;margin: 20px 0px;position: relative;float: left;width: 100%;">Bem vindo ao WebSheep <?=$ws_version->version?></strong>
					<br>
					<div class="description">
						Antes de começar, gostariamos de agradecer por escolher e utilizar nossa plataforma, 
						pois ela é fruto de muito esforço e noites sem dormir.
						Ela foi projetada especialmente para profissionais na área de WEB que nao querem perder tempo nem dinheiro com desenvolvimentos complexos sob medida para seus clientes.
					</div>
					<?
							echo 	'<div class="avisophp">
										<div class="response"> 
											<label>
											<input id="eu_aceito" value="0" type="checkbox"/>
											Eu aceito os termos de privacidade <br> e termos GNU GENERAL PUBLIC LICENSE do painel WebSheep.</label>
										</div>
									</div>';
					?>
					<div class="step botao vamosla" id="vamosla">Instalar painel WebSheep</div>
					<div id='loader' style="text-align: center;position: relative;float: right;right: -223px;top: 80px;"></div>   
				</div>   
			</div>   
	</div>
</body>