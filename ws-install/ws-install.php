<?
	ini_set('max_execution_time',0);
	function ws_copy_dir($src,$dst) { 
		$dir = opendir($src); 
		@mkdir($dst); 
		while(false !== ( $file = readdir($dir)) ) { 
			if (( $file != '.' ) && ( $file != '..' )) { 
				if ( is_dir($src . '/' . $file) ) { 
					ws_copy_dir($src . '/' . $file,$dst . '/' . $file); 
				} else { 
					copy($src . '/' . $file,$dst . '/' . $file); 
				} 
			} 
		} 
		closedir($dir); 
	} 
	function ws_delete_dir($Dir) {
		if ($dd = opendir($Dir)) {
			while (false !== ($Arq = readdir($dd))) {
				if ($Arq != "." && $Arq != "..") {
					$Path = "$Dir/$Arq";
					if (is_dir($Path)) {
						ws_delete_dir($Path);
					} elseif (is_file($Path)) {
						unlink($Path);
					}
				}
			}
			closedir($dd);
		}
		rmdir($Dir);
	}
	if(isset($_GET['download']) && $_GET['download']=='1'){
			$folderName = "CMS-".str_replace(".zip","",basename($_REQUEST['branche']));
			$fp 		= fopen('ws-update.zip', 'w');
			fwrite($fp,file_get_contents($_REQUEST['branche']));
			fclose($fp);
			$zip = new ZipArchive();
			if ($zip->open("./ws-update.zip")) {
				$zip->extractTo("./");
				$zip->close();
				if(file_exists("./../admin")){ws_delete_dir("./../admin");}
				verifyAdmin:
				if(!file_exists("./../admin")){
					$dst = "./../";
					$dir = opendir("./".$folderName); 
					while(false !== ( $file = readdir($dir)) ) { 
						if (( $file != '.' ) && ( $file != '..' )) { 
							$remetente = $folderName . '/' . $file;
							$destino = $dst . '/' . $file;
							if (is_dir($remetente)) { 
								@mkdir($destino);
								ws_copy_dir($remetente,$destino);
							} else { 
								copy($remetente,$destino); 
							} 
						} 
					} 
					closedir($dir); 
					ws_delete_dir("./".$folderName);
					unlink("./ws-update.zip");
				}else{
					goto verifyAdmin;
				}
				echo "sucesso";
			}
		exit;
	}
	$remoteVersion = json_decode(file_get_contents("https://raw.githubusercontent.com/websheep/cms/master/admin/App/Templates/json/ws-update.json"));
?>

<head>
<title><?
	if(file_exists("./../admin")){
		echo "WebSheep - Update de sistema";
	}else{
		echo "WebSheep - instalação do sistema";
	}
?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<link rel="icon" sizes="16x16" href="//raw.githubusercontent.com/websheep/cms/master/admin/App/Templates/img/websheep/arrow-circle-225.png" />
<meta http-equiv="pragma" content="no-cache" />
<link href="https://fonts.googleapis.com/css?family=Titillium+Web:400,700" rel="stylesheet">

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
		font-family:'Titillium Web', sans-serif;
		font-style: normal;
		font-weight: 700;
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
		font-style: normal;
		border-radius: 7px;
	}
	.txt b{
		font-family:'Titillium Web', sans-serif;
		font-style:normal;
		font-weight:400;

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
		background-image: url(//raw.githubusercontent.com/websheep/cms/master/admin/App/Templates/img/websheep/loading322.gif);
		width: 32px;
		height: 32;
		float: left;
		background-repeat: no-repeat;
		left: 50%;
		top: 68px;
		transform: translateX(-50%);
		position: absolute;
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
		font-family:'Titillium Web', sans-serif;
		font-style:normal;
		font-weight:400;
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
		font-family:'Titillium Web', sans-serif;
		font-style:normal;
		font-weight:400;
		color: #d4e3eb;
		text-shadow: -1px -1.5px 1px #004674;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}
	#branches{
		padding: 10px;
		width: 100%;
	   -webkit-appearance: button;
	   -webkit-border-radius: 2px;
	   -webkit-box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1);
	   -webkit-padding-end: 20px;
	   -webkit-padding-start: 2px;
	   -webkit-user-select: none;
	   background-image: url(http://i62.tinypic.com/15xvbd5.png), -webkit-linear-gradient(#FAFAFA, #F4F4F4 40%, #E5E5E5);
	   background-position: 97% center;
	   background-repeat: no-repeat;
	   border: 1px solid #AAA;
	   color: #555;
	   font-size: inherit;
	   overflow: hidden;
	   padding: 5px 10px;
	   text-overflow: ellipsis;
	   white-space: nowrap;
	   width: 300px;
	}
	.version{
		position: relative;
		font-size: 14px;
		font-weight: 600;
		color: #7d7d7d;
	}
</style>
</head>
<div class="comboCentral">
	<div class="logo"><b><?if(file_exists("./../admin")){echo "Update"; }else{echo "Setup";}?> WebSheep</b><br>
	</div>
	<div class="txt" id="txt">
		<select id="branches"></select>
	</div>
	<!--  -->

	<div class="botao" id="botao">instalar WebSheep</div>
	<div class="loader" id="loader" style="display:none"></div>

</div>
<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script type="text/javascript">

window.setMsn = function(valor){$('#bytes').text(valor);}
$(document).ready(function(){
	$("#botao").hide();
	$.getJSON("https://api.github.com/repos/websheep/cms/branches",function(a){
		$.each(a,function(b,c){
			var baseLink = "https://github.com/websheep/cms/archive/__.zip";
			var realLink = baseLink.replace("__",c.name);
			$("#branches").prepend('<option value="'+realLink+'">'+c.name+'</option>');
		})
		$("#botao").show();
	})
	$("#botao").bind("click press tap",function(){
		$("#loader").show();
		$("#txt,#botao").hide();

		$.ajax({
			url:"./<?=basename(__FILE__)?>",
			data:{download:1,branche:$("#branches").val()},
			beforeSend:function( xhr ) {}
		}).done(function( data ) {
			if(data=="sucesso"){
				window.location = "/admin/";
			}else{
				alert(data);
			}
		});
	});

})
</script>