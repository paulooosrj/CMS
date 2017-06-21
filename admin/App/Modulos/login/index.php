<?
	header("Content-Type: text/html; charset=utf-8",true);
	error_reporting(E_ALL); 
?>

<html lang="pt-br">
<head>
<meta charset="UTF-8">
<link type="image/x-icon" href="img/favicon.ico" rel="shortcut icon" />
<meta name="viewport"  content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="/admin/App/Templates/css/fontes/fonts.css" 											type="text/css" media="all" />
<link rel="stylesheet" href="/admin/App/Templates/css/websheep/modulos/login/estrutura.css" 						type="text/css" media="all" />
<link rel="stylesheet" href="/admin/App/Templates/css/websheep/modulos/login/desktop.css?v=?<?=rand(0,999999)?>" 	type="text/css" media="all" />
<link rel="stylesheet" href="/admin/App/Templates/css/chosen/chosen.css" 											type="text/css" media="all" />
<link rel="stylesheet" href="/admin/App/Templates/css/websheep/theme_blue.min.css" 									type="text/css" media="all" />
<link rel="stylesheet" href="/admin/App/Templates/css/websheep/funcionalidades.css" 								type="text/css" media="all" />
<script type = 'text/javascript' src="/admin/App/Vendor/jquery/2.2.0/jquery.min.js"							id="jquery"></script>
<script type = 'text/javascript' src="/admin/App/Templates/js/websheep/websheep_full.js" 							id="websheep_full"></script>
<script type = 'text/javascript' src="/admin/App/Templates/js/websheep/funcionalidades.min.js" 						id="funcionalidades"></script>
<link href="https://fonts.googleapis.com/css?family=Titillium+Web:300,400,600,700" rel="stylesheet">
<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<style type="text/css">
iframe{
	position: relative;
	width: 190px;
	height: 75px;
	float: left;
	top: -15px;
}
.g-recaptcha div:first-child{
    position: relative;
    float: left;
    width: 100%!important;
    height: 100%!important;
}
</style>
<body id="body"> 
<div id='avisoTopo'></div>
<div id="container">
<div id="login">
	<div id="logomarca">
        <spam id="h3"><?=file_get_contents(ROOT_ADMIN.'/App/Templates/txt/ws-version.txt')?></spam>
        <img src="/admin/App/Templates/img/websheep/logoIcon.png">
    </div>
	<form  id="formulario">
		<input 		class="inputText" name="usuario"  		value=""			placeholder="Usuario:"/></input>
		<input  	class="inputText" name="senha" 			value=""			placeholder="Senha:" type="password"/></input>
		<input  	class="inputText" name="function" 		value="login" 		hidden="true"> 	
		<div 		id="iniciarsessao_disabled" class="botaodisabled "><?=ws::getlang("login>loading");?></div>
		<?if(defined('RECAPTCHA') && RECAPTCHA!=""):?>
			<div class="g-recaptcha" data-sitekey="<?=RECAPTCHA?>" data-callback="wsSetLogin" data-expired-callback="dataexpiredcallback" style="position:relative;float:left;width: 180px;height: 43px;margin-right: 10px;overflow: hidden;border: solid 5px #2e5994;"></div> 
			<button 	id="iniciarsessao" type="submit" class='botao ' ><?=ws::getlang("login>buttomBeginSession");?></button>
		<?else:?>
			<button 	id="iniciarsessao" type="submit" class='botao ' style=" width: 400px;" ><?=ws::getlang("login>buttomBeginSession");?></button>
		<?endif;?>
		<div 		id="" class="w1 esqueci">
			<a href="#" id="esqueceu" class="email"><?=ws::getlang("login>recoverPassword");?></a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="#" id="politica" class="politica"><?=ws::getlang("login>privacypolicy");?></a>
		</div>
		<a id="tokenRequest"></a>

	</form>
	<div class="c"></div> 
</div>
<script type="application/javascript">
		$('#iniciarsessao_disabled').hide()
		function dataexpiredcallback(a){ alert(a); }
		function wsSetLogin(a){
			$("#iniciarsessao").unbind('click tap press').bind('click tap press',function(e) {
				e.preventDefault();
				$('#iniciarsessao_disabled').show();
				$.ajax({
					type: "POST",
					async: true,
					url: "/admin/App/Modulos/login/functions.php",
					data:{'function':'login','form':$("#formulario").serialize()},
					beforeSend: function() {
						$("#iniciarsessao").hide('fast')
						$("#iniciarsessao_disabled").show('fast')
						confirma({width: "auto", conteudo: " <?=ws::getlang("login>loading");?><div class=\'preloaderupdate\' style=\'left: 50%;margin-left: -15px; position: absolute;width: 30px;height: 18px;top: 53px;background-image:url(\"/admin/App/Templates/img/websheep/loader_thumb.gif\");background-repeat:no-repeat;background-position: top center;\'></div>", drag: false, bot1: 0, bot2: 0 })
					}
				}).done(function(e){
					out(e)
					if(e.indexOf('ok')!= -1){
						window.location.reload();
					}else{
						$("#iniciarsessao").show('fast');
						$("#iniciarsessao_disabled").hide('fast');
						alert(e)
					}
				})
			})
		}
		<?if(defined('RECAPTCHA') && RECAPTCHA!=""):?>
			$("#formulario").submit(function(e) { e.preventDefault();alert("<?=ws::getlang("login>responseToken");?>");return false;})
		<?else:?>
			wsSetLogin();
		<?endif;?>



		$("#esqueceu").click(function(e) {
			e.preventDefault();
			confirma({
				conteudo:'<?=ws::getlang("login>modalRecover");?><br><input id="emailUser" class="inputText" placeholder="Seu e-mail:" style="padding: 10px 30px; width:90%;margin: 10px;">',
				posFn:function(e){},
				newFun:function(){
					$email = $("#emailUser").val();
					setTimeout(function(){
						confirma({conteudo:"<?=ws::getlang("login>recovering");?>", bot2:false, bot1:false})

						$.ajax({
							type: "POST",
							url: "/admin/App/Modulos/login/functions.php",
							data: {
								'function': 'enviaemail',
								'mail': $email
							}
						}).done(function(data) {
								confirma({conteudo:data, bot2:false, bot1:"ok"}) 
						});


					},500)
				},
				width:600,
				height:'auto',
				divScroll:"#body",
				drag:false,
				bot2:'Cancelar',
				bot1:"Enviar senha por email",
				Callback:function(e){},
				check:function(e){return true}
			})
		})
		$("#politica").click(function(e) {
			e.preventDefault();
			confirma({
				conteudo:<? echo "'"
				.'<h1 class="w1" style="font-size:20px;font-weight:bold;">'
					.'<i class="icon-docs" style="margin-right: 10px;"></i>'
					.'Política de privacidade'
				.'</h1>'
				.'<div id="termos" class="inputText" style="position: relative;background: #FFF;margin: 10px;padding: 10px;padding-right: 16px;text-align: justify;font-size: 14px;height: 300px;max-;overflow: auto;">'.str_replace(array(PHP_EOL,"'"),array("<br>","\'"),file_get_contents($_SERVER['DOCUMENT_ROOT'].'/admin/App/Templates/txt/'.ws::getlang("login>privacypolicyURL"))).'</div>'
				."'";
				?>,
				posFn:function(e){},
				newFun:function(){},
				width:450,
				height:'auto',
				divScroll:"#body",
				drag:false,
				bot2:false,
				bot1:"Ok, entendi",
				Callback:function(e){},
				check:function(e){return true}
			})
		})

		$("#tokenRequest").click(function(e) {
			e.preventDefault();  
			confirma({
				conteudo:<? echo "'"
								.ws::getlang("login>newPass")
								.'<br>'
								.'<form id="getRequest">'
									.'<input id="" 			class="inputText" 	name="tokenRequest" type="hidden" value="'.@$_GET['tokenRequest'].'">'
									.'<input id="newPass" 	class="inputText" 	name="newPass" 		type="password" placeholder="Nova senha:" style="padding: 10px 30px; width:90%;margin: 10px;"><br>'
									.'<input id="newPass2" 	class="inputText" 	name="newPass2" 	type="password" placeholder="Digite novamente sua senha:" style="padding: 10px 30px; width:90%;margin:0 10px;">'
								.'</form>'
								."'";?>,
				posFn:function(e){},
				newFun:function(){
					var formulario = $("#getRequest").serialize();
					setTimeout(function(){
						confirma({conteudo:"Solicitando alterações...", bot2:false, bot1:false, drag:false, newFun:function(){}})
						$.ajax({
							type: "GET",
							async: true,
							url: "/admin/App/Modulos/login/functions.php",
							data:{'function':'setNewPass','form':formulario}
						}).done(function(e){
							if(e==true){
								setTimeout(function(){
									confirma({conteudo:"<?=ws::getlang("login>passwordChangedSuccessfully")?>", botclose:true, bot2:false, bot1:false, drag:false, onClose:function(){window.location = "/admin";}})
								},500)
							}else{alert(e)}
						})
					},500)
				},
				width:600,
				drag:false,
				bot1:"Cadastrar",
				bot2:'Cancelar',
				Callback:function(e){},
				ErrorCheck:function(e){TopAlert({mensagem:"<?=ws::getlang("login>passwordsDoNotMatch")?>",type:2}) },
				Check:function(e){if($("#newPass").val()!=$("#newPass2").val()){return false;}else{return true;} }
			})	
			return false;
		})



		<?if(isset($_GET['tokenRequest']) && $_GET['tokenRequest']!=""):?>
					$.ajax({
						type: "GET",
						async: true,
						url: "/admin/App/Modulos/login/functions.php",
						data:{'function':'verifyToken','tokenRequest':'<?=$_GET['tokenRequest']?>'}
					}).done(function(e){
						if(e==true){
							$("#tokenRequest").click();
						}else{
							confirma({
								conteudo:"<?=ws::getlang("login>invalidOrExpiredToken")?>", 
								bot2:"Cancelar", 
								bot1:'<?=ws::getlang("login>ButtomSendAnotherLink")?>',
								newFun:function(){
									setTimeout(function(){
										$("#esqueceu").click();
									},500)
								}
							})
						}
					});





		<?endif;?>


</script>
</div>
</body>
</html>