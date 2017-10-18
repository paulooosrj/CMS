<?php
	##########################################################################################################
	# IMPORTA CLASSE PADRÃO DO SISTEMA
	##########################################################################################################
	include_once(__DIR__.'/../../Lib/class-ws-v1.php');
	_session();
	clearstatcache();

	##########################################################################################################
	# DEFINE O PATH DO MÓDULO
	##########################################################################################################
	define("PATH",'App/Modulos/_modulo_');

	##########################################################################################################
	# IMPORTA A CLASSE DE TEMPLATE
	##########################################################################################################	
	$template 						=	new Template(ROOT_ADMIN."/App/Templates/html/ws-central-bkp.html", true);

	##########################################################################################################
	# DEFINIMOS AS STRINGS PRINCIPAIS PUXANDO DO JSON
	##########################################################################################################
	$template->PATH												=	PATH;
	$template->central_Bkp_title								=	ws::getlang('centralBkp>title');
	$template->central_Bkp_createBackup							=	ws::getlang('centralBkp>createBackup');
	$template->central_Bkp_wait									= 	ws::getlang('centralBkp>wait');
	$template->central_Bkp_loading								=	ws::getlang('centralBkp>loading');
 	$template->central_Bkp_mycomputer							=	ws::getlang('centralBkp>mycomputer');
	$template->central_Bkp_library								=	ws::getlang('centralBkp>library');
	$template->central_Bkp_delete								=	ws::getlang('centralBkp>delete');
	$template->central_Bkp_creating								=	ws::getlang('centralBkp>creating');
	$template->central_Bkp_export								=	ws::getlang('centralBkp>export');
	$template->central_Bkp_cancel								=	ws::getlang('centralBkp>cancel');
	$template->central_Bkp_error								=	ws::getlang('centralBkp>error');
	$template->central_Bkp_restoration							=	ws::getlang('centralBkp>restoration');
	$template->central_Bkp_update								=	ws::getlang('centralBkp>update');
	$template->central_Bkp_refresh								=	ws::getlang('centralBkp>refresh');
	$template->central_Bkp_recharge								=	ws::getlang('centralBkp>recharge');		
	$template->central_Bkp_restore								=	ws::getlang('centralBkp>restore');	
	$template->central_Bkp_placeholderTitle						=	ws::getlang('centralBkp>placeholderTitle');				
	$template->central_Bkp_descrition							=	ws::getlang('centralBkp>descrition');				
	$template->central_Bkp_creatingatemplate					=	ws::getlang('centralBkp>creatingatemplate');
	$template->centralBkp_restoreModal_aboutTheFiles			=	ws::getlang('centralBkp>restoreModal>aboutTheFiles');
	$template->centralBkp_restoreModal_restore100				=	ws::getlang('centralBkp>restoreModal>restore100');
	$template->centralBkp_restoreModal_applyReplace				=	ws::getlang('centralBkp>restoreModal>applyReplace');
	$template->centralBkp_restoreModal_applyIgnore				=	ws::getlang('centralBkp>restoreModal>applyIgnore');
	$template->centralBkp_restoreModal_tools					=	ws::getlang('centralBkp>restoreModal>tools');
	$template->centralBkp_restoreModal_dataBase					=	ws::getlang('centralBkp>restoreModal>dataBase');
	$template->centralBkp_restoreModal_dontTouchDataBase		=	ws::getlang('centralBkp>restoreModal>dontTouchDataBase');
	$template->centralBkp_restoreModal_basicStructure			=	ws::getlang('centralBkp>restoreModal>basicStructure');
	$template->centralBkp_restoreModal_rememberIf				=	ws::getlang('centralBkp>restoreModal>rememberIf');
	
	##########################################################################################################
	# CASO NÃO TENHA O DIRETÓRIO, CRIA A PASTA PADRÃO DE BKP
	##########################################################################################################
	if(!file_exists(ROOT_ADMIN.'/../ws-bkp')){mkdir(ROOT_ADMIN.'/../ws-bkp');}

	##########################################################################################################
	# FAZ O WHILE NAS PASTAS LISTADAS E RERTORNA O TEMPLATE COM O NOME E LINK
	##########################################################################################################
	$dh = opendir(ROOT_ADMIN.'/../ws-bkp');
	while($diretorio = readdir($dh)){
		if($diretorio != '..' && $diretorio != '.' && substr($diretorio,-3)=="zip"){
			$newSplashScreen= "";			
			$template->dataFILE			=	$diretorio;
			$newSplashScreen= "";			
			$template->titulo			=	$diretorio;
			$template->label			=	_getLangMsn('ws000029');
			##########################################################################################################
			# ABRIMOS O ZIP PARA VERIFICAR O CONTEÚDO
			##########################################################################################################
				$fileZip = ROOT_ADMIN.'/../ws-bkp/'.$diretorio;
				$zip = new ZipArchive();
				if($zip->open($fileZip) === TRUE ){
					##########################################################################################################
					# VERIFICA SE EXISTE O ws-info.json
					##########################################################################################################
					if(strlen($zip->getFromName('ws-info.json'))){
					  		$contents 				= json_decode(trim($zip->getFromName('ws-info.json')),true);
					  		$thumb 					= base64_encode($zip->getFromName($contents['thumb']));
					  		$template->titulo 		= $contents['title'];
					    	$template->description 	= $contents['content'];

						    if($contents['thumb']!='ws-img/200/200/null'){
								$template->ClasseThumb		= 'minThumb';
								$template->newSplashScreen	= 'data:image/jpeg;base64,'.$thumb;
								$template->block("avatarTemplate");
						    }else{
								$template->ClasseThumb		= 'minThumb';
								$template->newSplashScreen	= $contents['thumb'];
								$template->block("avatarTemplate");
								$template->clear("newSplashScreen");
							}
				    }else{
						$template->clear("ClasseThumb");
						$template->clear("description");
				    }
					$zip->close();
				}else{
					$template->clear("ClasseThumb");
					$template->clear("description");
					$template->clear("avatarTemplate");
					$template->clear("newSplashScreen");
				}
			########################################################### pega a miniatura do zip
			$template->block("BLOCK_TEMPLATES");
		}
	}

	##########################################################################################################
	# RETORNA O HTML DO TEMPLATE NA TELA
	##########################################################################################################
	$template->block("BLOCK_CENTRAL_BKP");
	$template->show();











