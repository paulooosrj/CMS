<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/admin/App/Lib/class-ws-v1.php');
	_session();
	clearstatcache();
	#####################################################  CONFIGURA DADOS GERAIS
	define("PATH",'App/Modulos/_modulo_');
	$template 						=	new Template(ROOT_ADMIN."/App/Templates/html/ws-central-bkp.html", true);
	$template->BOT_RESTAURAR_BKP	=	_getLangMsn("ws000048");
	$template->TITULO				=	_getLangMsn('ws000049');
	$template->LABEL_BOT_TOP		=	_getLangMsn('ws000050');
	$template->PATH					=	PATH;
	if(!file_exists(ROOT_ADMIN.'/../ws-bkp')){ 
		mkdir(ROOT_ADMIN.'/../ws-bkp');
	}
	$dh = opendir(ROOT_ADMIN.'/../ws-bkp');
	while($diretorio = readdir($dh)){
		if($diretorio != '..' && $diretorio != '.' && substr($diretorio,-3)=="zip"){
			$newSplashScreen= "";			
			$template->titulo			=	$diretorio;
			$template->label			=	_getLangMsn('ws000048');
			########################################################### pega a miniatura do zip
				$fileZip = ROOT_ADMIN.'/../ws-bkp/'.$diretorio;
				$zip = new ZipArchive();
				if($zip->open($fileZip) === TRUE ){
				    if(strlen($zip->getFromName('ws-description.txt'))){
				  		$ws_description 		= explode(PHP_EOL,$zip->getFromName('ws-description.txt'));
				  		$title 					= array_slice($ws_description,0,1);
				  		$description 			= implode(array_slice($ws_description,1),"<br>");
				  		$template->titulo 		= $title[0];
				    	$template->description 	= $description;
				    }else{
						$template->clear("description");
				    }
				    if(strlen($zip->getFromName('ws-thumb.jpg'))){
				  		$base64 = 'data:image/jpeg;base64,'.base64_encode($zip->getFromName('ws-thumb.jpg'));
						$template->ClasseThumb	= 'minThumb';
						$template->newSplashScreen	= $base64;
						$template->block("avatarTemplate");
				    }else{
						$template->clear("ClasseThumb");
						$template->clear("avatarTemplate");
						$template->clear("newSplashScreen");
				    }
					$zip->close();
				}else{
					$template->clear("avatarTemplate");
					$template->clear("newSplashScreen");
				}
			########################################################### pega a miniatura do zip
			$template->block("BLOCK_TEMPLATES");
		}
	}
$template->block("BLOCK_CENTRAL_BKP");
$template->show();
