<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/admin/App/Lib/class-ws-v1.php');

$link = file_get_contents(ROOT_ADMIN.'/App/Templates/install/link.txt');
set_time_limit(-1);
ini_set('max_execution_time', -1);
function _excluiDir($Dir){
	if(file_exists($Dir) && is_dir($Dir)){
	   if ($dd = opendir($Dir)) {
	        while (false !== ($Arq = readdir($dd))) {
	            if($Arq != "." && $Arq != ".."){
	                $Path = "$Dir/$Arq";
	                if(is_dir($Path)){
	                    _excluiDir($Path);
	                }elseif(is_file($Path)){
	                    unlink($Path);
	                }
	            }
	        }
	        closedir($dd);
	    }
	    rmdir($Dir);
	}else{
		echo "O diretório '".$Dir."' não existe!";
		exit;
	}
}
	if(isset($_GET['download']) && $_GET['download']=='1'){
		include(ROOT_ADMIN.'/includes/classes/class-mega.php');
		$megafile = new MEGA($link);
		if($megafile->download_file()){
			$infos 	= $megafile->file_info();
			$file 	= $infos['attr']['n'];
			_excluiDir(ROOT_ADMIN);
			$zip 	= new ZipArchive();
			if (substr($file,-3)=="zip" && $zip->open('./'.$file) === TRUE) {
				if($zip->extractTo('./')){
					echo json_encode(array("function"=>"download","status"=>"sucesso","response"=>'<script>window.location="./admin"</script>'));
				}else{
					echo json_encode(array("function"=>"download","status"=>"falha","response"=>'<span style=\'color:#E42C00;\'>Houve alguma falha na descompressão do pacote,Acesse: www.websheep.com.br/faq</span>'));
				}
				if($zip->close()){
					if(is_file($file)) {
						if(is_writable($file)) {
							unlink($file);
						}
					}
				};
				exit;
			}
		}
		exit;
	}
	if(isset($_GET['init']) && $_GET['init']=='1'){
			include(ROOT_ADMIN.'/App/Lib/class-mega.php');
			$megafile = new MEGA($link);
			echo $megafile->Get_File_Name(); 
			exit;
	}
?>

<head>
<title>WebSheep - Setup</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<link rel="icon" href="https://mega.nz/favicon.ico?v=2" type="image/x-icon" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
<link href="https://fonts.googleapis.com/css?family=Titillium+Web" rel="stylesheet">
<style type="text/css">
	*{margin:0;padding:0;}
	body{
		background: #63b1ed;
		background: -moz-radial-gradient(center, ellipse cover,  #63b1ed 0%, #0086c4 99%);
		background: -webkit-radial-gradient(center, ellipse cover,  #63b1ed 0%,#0086c4 99%);
		background: radial-gradient(ellipse at center,  #63b1ed 0%,#0086c4 99%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#63b1ed', endColorstr='#0086c4',GradientType=1 );
	}
	.comboCentral{
		position: fixed;
		padding-bottom: 40px;
		width: 400px;
		background-color: #FFF;
	    left: 50%;
	    top: 50%;
	    transform: translateX(-50%) translateY(-50%);
		background-color: #ffffff;
		border: 1px solid #abcad6;
		-moz-border-radius: 7px;
		-webkit-border-radius: 7px;
		border-radius: 7px;
		-moz-box-shadow: 	inset 0px 0px 0px 1px #ffffff,		0px 10px 20px -5px rgba(9, 27, 66, 0.56);
		-webkit-box-shadow: inset 0px 0px 0px 1px #ffffff,		0px 10px 20px -5px rgba(9, 27, 66, 0.56);
		box-shadow: 		inset 0px 0px 0px 1px #ffffff,		0px 10px 20px -5px rgba(9, 27, 66, 0.56);
		filter: progid:DXImageTransform.Microsoft.gradient(startColorstr = '#ffffff', endColorstr = '#d6d6d6');
		-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr = '#ffffff', endColorstr = '#d6d6d6')";
		background-image: -moz-linear-gradient(top, #ffffff, #d6d6d6);
		background-image: -ms-linear-gradient(top, #ffffff, #d6d6d6);
		background-image: -o-linear-gradient(top, #ffffff, #d6d6d6);
		background-image: -webkit-gradient(linear, center top, center bottom, from(#ffffff), to(#d6d6d6));
		background-image: -webkit-linear-gradient(top, #ffffff, #d6d6d6);
		background-image: linear-gradient(top, #ffffff, #d6d6d6);
		-moz-background-clip: padding;
		-webkit-background-clip: padding-box;
		background-clip: padding-box;
	}
	.logo{
		font-family: 'Titillium Web', sans-serif;
		font-style: normal;
		font-weight: bolder;
		font-size: 36px;
		top: -10px;
		position: relative;
		color: #0073B9;
		text-align: center;
		padding-top: 20px;
    }
	.txt{
		position: relative;
		text-align: center;
		font-family: 'Titillium Web', sans-serif;
		font-style:normal;
		font-weight:300;
	}
	.txt b{
		font-family: 'Titillium Web', sans-serif;
		font-style:normal;
		font-weight:700;
	}
	.password{
		padding: 10px;
		text-align: center;
		margin-top: 10px;
	    margin-right: 10px;
	    width: 320px;
		position: relative;
		left: 50%;
		transform: translateX(-50%);
	}
	.loader{
		background-image: url(data:image/gif;base64,R0lGODlhEAAQAPMAAOTx+Dii14TF5QaKzSaZ09br9qjW7RaS0Ga34Mbj8pjO6fj7/Eip2na+41av3P///yH/C05FVFNDQVBFMi4wAwEAAAAh+QQJCgAAACwAAAAAEAAQAAAETBDISau9dYXBh2zCYmmddAxEkRElYLCEOAnoNi2sQG2GRhmDAIWDIU6MGSSAR0G4ghRa7IjIUXAog6QzpRRYhy0nILsKGuJxGcNuTyIAIfkECQoADAAsAgAAAAwAEAAABFWQyXKUvJiREVJmi3AMZIEVGzkgIABuhDIcwBIQ4YAzgfAOgsDAICEWOaQPSDVYKElC48Jo4Ah0zuBPsCCwACMFzOliIJgE06WgOnA/CSFBeVEc1IwIACH5BAkKAA0ALAIAAAAMABAAAARTsMnZAKO4qWHoEsEwHOMiFYSoikKzpESXiEcnDITZ3IgedhJBwWW4DTKSFXLBMpgKLYlhEJAsEIPoK9swkAaJhQFmMq5wQxfpGxDopAMFcsIAICMAIfkECQoAAAAsAgABAA0ADQAABEYQyEnBWnKJMEYQV5AABdGdA8Eli0kYkmF2iZBiGUc3BzzptIpQkigWcRUUgmI4NACn5aQ1EEA7r4vsBnXMUISCZKnRfZARACH5BAkKAAwALAAAAgAQAAwAAARUkEm2RBgjiDU7IFg4EEU3IYTBGSDBTSXVLaDAlMmAACajDAfCgXEBbiSGIkbxCwUME8EBE2AEp4oeADFI3BiC0Uuy4MTILdWCFRjDQKIRr1cpatwRACH5BAUKAA0ALAAAAgAQAAwAAARTsMkpxghizbmEaoZlEcVmHMOxaCAxECwjDsi2uIIEKKhVTqHAxpIQ5IYDJEtiTFg2gYFhUxAdFADma9lAzBiS28ugWaEO00nBJZoaMpuwIDo4biIAOw==);
		width: 19px;
		height: 17px;
		float: left;
		background-repeat: no-repeat;
		left: 50%;
		top: 20px;
		transform: translateX(-50%);
		position: relative;
	}
	.botao:hover{
		cursor: pointer;
		background-color: #fff;
		border: 1px solid #83a2cb;
		-moz-border-radius: 7px;
		-webkit-border-radius: 7px;
		border-radius: 7px;
		-moz-box-shadow: inset 0px 1px #a6d0ff;
		-webkit-box-shadow: inset 0px 1px #a6d0ff;
		box-shadow: inset 0px 1px #a6d0ff;
		filter: progid:DXImageTransform.Microsoft.Shadow(strength = 1,direction = 180,color = '#a6d0ff');
		-ms-filter: progid:DXImageTransform.Microsoft.Shadow(strength = 1,Direction = 180,Color = '#a6d0ff');
		filter: progid:DXImageTransform.Microsoft.gradient(startColorstr = '#5f92d7',endColorstr = '#365f97');
		-ms-filter: progid:DXImageTransform.Microsoft.gradient(startColorstr = '#5f92d7',endColorstr = '#365f97');
		background-image: -moz-linear-gradient(top,#5f92d7,#365f97);
		background-image: -ms-linear-gradient(top,#5f92d7,#365f97);
		background-image: -o-linear-gradient(top,#5f92d7,#365f97);
		background-image: -webkit-gradient(linear,center top,center bottom,from(#5f92d7),to(#365f97));
		background-image: -webkit-linear-gradient(top,#5f92d7,#365f97);
		background-image: linear-gradient(top,#5f92d7,#365f97);
		-moz-background-clip: padding;
		-webkit-background-clip: padding-box;
		background-clip: padding-box;
		font-family: 'Titillium Web', sans-serif;
		font-style:normal;
		font-weight:300;
		color: #fff;
		text-shadow: -1px -1.5px 1px #004674;
	}
	.botao{
		top: 14px;
		left: 50%;
		transform: translateX(-50%);
	    margin-top: 10px;
		position: relative;
		float: left;
		padding: 10px 70px;
		cursor: pointer;
		background-color: #fff;
		border: 1px solid #497cbf;
		-moz-border-radius: 7px;
		-webkit-border-radius: 7px;
		border-radius: 7px;
		-moz-box-shadow: inset 0px 1px #9bf;
		-webkit-box-shadow: inset 0px 1px #9bf;
		box-shadow: inset 0px 1px #9bf;
		filter: progid:DXImageTransform.Microsoft.Shadow(strength = 1,direction = 180,color = '#99bbff');
		-ms-filter: progid:DXImageTransform.Microsoft.Shadow(strength = 1,Direction = 180,Color = '#99bbff');
		filter: progid:DXImageTransform.Microsoft.gradient(startColorstr = '#497cbf',endColorstr = '#365f97');
		-ms-filter: progid:DXImageTransform.Microsoft.gradient(startColorstr = '#497cbf',endColorstr = '#365f97');
		background-image: -moz-linear-gradient(top,#497cbf,#365f97);
		background-image: -ms-linear-gradient(top,#497cbf,#365f97);
		background-image: -o-linear-gradient(top,#497cbf,#365f97);
		background-image: -webkit-gradient(linear,center top,center bottom,from(#497cbf),to(#365f97));
		background-image: -webkit-linear-gradient(top,#497cbf,#365f97);
		background-image: linear-gradient(top,#497cbf,#365f97);
		-moz-background-clip: padding;
		-webkit-background-clip: padding-box;
		background-clip: padding-box;
		font-family: 'Titillium Web', sans-serif;
		font-style:normal;
		font-weight:300;
		color: #d4e3eb;
		text-shadow: -1px -1.5px 1px #004674;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}
	#txt{
		font-family: 'Titillium Web', sans-serif;
		text-align: center;
	}
</style>
</head>
<div class="comboCentral">
	<div class="logo"><b>Atualização WebSheep</b></div>
	<input class="password" id="password">
	<div class="botao" id="botao">Verificar link e instalar painel </div>
	<div id="txt"></div>
	<div class="loader" id="loader" style="display:none"></div>
	<div class="txt" id="bytes"></div>

</div>
<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="/admin/App/Templates/js/websheep/functionsws.min.js"></script>
<script type="text/javascript">
window.setMsn = function(valor){$('#bytes').text(valor);}

$(document).ready(function(){
	$("#botao").bind("click press tap",function(){
		var keyPass = $('#password').val();
			$.ajax({
				url:"./<?=basename(__FILE__)?>",
				data:{init:1,keyPass:keyPass},
				beforeSend:function( xhr ) {
					$("#txt").text("Procurando pacote solicitado...");
					$("#loader").show();
					$("#password, #botao").hide();
				}
			}).done(function( data ) {
				json 	= eval($.parseJSON(data));
				console.log(json)
				if(json.status=="download"){
					$("#txt").html(json.response);
					$("#loader").show();
					$.ajax({
						url:"./<?=basename(__FILE__)?>",
						data:{download:1,password:$("#password").val()},
						beforeSend:function( xhr ) {}
					}).done(function( data ) {
						json 	= eval($.parseJSON(data));
						if(json.status=="sucesso"){
							$("#txt").html(json.response);
							$("#loader").show();
						}else{
							$("#txt").html(json.response);
							$("#loader").hide();
						}
					});
				}else{
					$("#txt").html(json.response);
					$("#loader").hide();
				}
			
			});
	}).click();
})
</script>