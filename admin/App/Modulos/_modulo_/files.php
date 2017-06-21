<?php

	#####################################################  
	# IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
	#####################################################  
	include_once($_SERVER['DOCUMENT_ROOT'].'/admin/App/Lib/class-ws-v1.php');

	#####################################################  
	# CRIA SESSÃO
	#####################################################  
	_session();

	#####################################################  
	# Limpa as informações em cache sobre arquivos
	#####################################################
	clearstatcache();

	#####################################################  
	# Limpa as informações em cache sobre arquivos
	#####################################################
	if(empty($_GET['id_item']) || $_GET['id_item']==""){$_GET['id_item']=0;}

	#####################################################  
	# Limpa as informações em cache sobre arquivos
	####################################################

	$template = new Template(ROOT_ADMIN."/App/Templates/html/Modulos/ws-tool-files.html", true);

	$template->ID_ITEM 			= $_GET['id_item'];
	$template->WS_NIVEL 		= $_GET['ws_nivel'];
	$template->WS_ID_FERRAMENTA = $_GET['ws_id_ferramenta'];
	$template->TOKEN_GROUP 		= $_GET['token_group'];

	if(criaRascunho($_GET['ws_id_ferramenta'],$_GET['id_item'])){
		$template->block("TOP_ALERT_RASCUNHO");
	}

	if(empty($_GET['direct'])){ 
		$template->block("BOT_BACK");
	}

	$draft				= new MySQL();
	$draft->set_table(PREFIX_TABLES."_model_item");
	$draft->set_where('ws_draft="1"');
	$draft->set_where('AND ws_id_draft="'.$_GET['id_item'].'"');
	$draft->select();

	$s 					= new MySQL();
	$s->set_table(PREFIX_TABLES.'_model_files');
	if((isset($_GET['original']) && $_GET['original']=='true')|| $draft->_num_rows==0){
		$s->set_where('id_item="'.$_GET['id_item'].'"');
	}else{
		$s->set_where('ws_draft="1"');
		$s->set_where('AND ws_id_draft="'.$_GET['id_item'].'"');
	}
	$s->set_order('posicao','ASC');
	$s->select();
	foreach($s->fetch_array as $_files_){
			$template->LI_ID 	=	$_files_['id'];
			$template->NOME 	=	$_files_['filename'];
			$template->ARQUIVO 	=	$_files_['file'];
			$template->LI_TOKEN =	$_files_['token'];
			$template->PESO 	=	_filesize($_files_['size_file']);
			$template->block("BLOCK_FILES_ITEM");
	}


	$s 					= new MySQL();
	$s->set_table(PREFIX_TABLES."ws_ferramentas");
	$s->set_where('id="'.$_GET['ws_id_ferramenta'].'"');
	$s->select();

	$template->EXTENCOES =	"'".str_replace(',',"','",$s->fetch_array[0]['_extencao_'])."'";

	$template->block("FILES");
	$template->show();
