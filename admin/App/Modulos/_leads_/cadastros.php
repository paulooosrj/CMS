<?php
	#####################################################  
	# FORMATA O CAMINHO ROOT
	#####################################################
	$r                        = $_SERVER["DOCUMENT_ROOT"];
	$_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;

	#####################################################  
	# DEFINE O PATH DO MÓDULO 
	#####################################################
	define("PATH", 'App/Modulos/_leads_');
		
	#####################################################  
	# LIMPA O CACHE INTERNO
	#####################################################
	clearstatcache();
	
	#####################################################  
	# CONTROLA O CACHE
	#####################################################
	header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	#####################################################  
	# IMPORTA A CLASSE PADRÃO DO SISTEMA
	#####################################################
	ob_start();
	include(__DIR__.'/../../Lib/class-ws-v1.php');
	
	#####################################################  
	# CRIA SESSÃO
	#####################################################  
	_session();

	#####################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	#####################################################
	verifyUserLogin();
	
	#####################################################  
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	#####################################################  
	define("TEMPLATE_LINK", ROOT_ADMIN . "/App/Templates/html/Modulos/_leads_/ws-tool-leads-cadastros.html");

	#####################################################  
	# PESQUISAMOS NA BASE O VALOR DO LEAD 
	#####################################################
	$title = new MySQL();
	$title->set_table(PREFIX_TABLES.'ws_list_leads');
	$title->set_where('token="'.$_GET['lead'].'"');
	$title->select();

	$local = new MySQL();
	$local->set_table(strtolower(PREFIX_TABLES.'wslead_'.$_GET['lead']));
	$local->show_columns();


	#####################################################  
	# MONTAMOS A CLASSE DOS TEMPLATES 
	#####################################################
	$template           = new Template(TEMPLATE_LINK, true);
	$template->PATH 	= 'App/Modulos/_leads_';
	$template->TITULO 	= $title->obj[0]->title;
	$template->LEAD 	= $_GET['lead'];

	#####################################################  
	# BLOCO DE TRADUÇÃO
	#####################################################
	
	$template->Leads_Register_Back						=	ws::getLang("Leads>Register>Back");
	$template->Leads_Register_Backing					=	ws::getLang("Leads>Register>Backing");	
	$template->Leads_Register_Modal_NumerForPage		=	ws::getLang("Leads>Register>Modal>NumerForPage");
	$template->Leads_Register_Modal_WantDelete			=	ws::getLang("Leads>Register>Modal>WantDelete");
	$template->Leads_Register_Modal_DataDelete			=	ws::getLang("Leads>Register>Modal>DataDelete");
	$template->Leads_Register_Modal_Delete				=	ws::getLang("Leads>Register>Modal>Delete");
	$template->Leads_Register_Modal_Cancel				=	ws::getLang("Leads>Register>Modal>Cancel");
	$template->Leads_Register_Modal_DeleteLink			=	ws::getLang("Leads>Register>Modal>DeleteLink");

	#####################################################  
	# VARREMOS AS COLUNAS DO LEAD E BLOCAMOS O TEMPLATE 
	#####################################################
	$cads = new MySQL();
	$cads->set_table(strtolower(PREFIX_TABLES.'wslead_'.$_GET['lead']));
	$cads->set_colum('id');
	foreach($local->fetch_array as $coluna){
		if($coluna['Field']!="id"){
			$cads->set_colum($coluna['Field']);
			$template->TH_FIELD 	= $coluna['Field'];
			$template->block("TH_THEAD"); 
		}
	} 
	$cads->select();

	#####################################################  
	# VARREMOS OS REGISTROS E RETORNAMOS A LISTA  
	#####################################################
	if($cads->_num_rows>0){
		foreach ($cads->fetch_array as $item) {
			foreach($local->fetch_array as $coluna){
				if($coluna['Field']!="id"){
					$template->TD_FIELD = $item[$coluna['Field']];
					$template->block("TD"); 
				}
			}
			$template->TD_ID = $item['id'];
			$template->block("TR_TABLE"); 
		}
	}

	#####################################################  
	# SE TIVER REGISTROS, PRINTA A FUNÇÃO JAVASCRIPT DATATABLE  
	#####################################################
	if($cads->_num_rows>0){
		$template->block("ATIVA_TABLE"); 
	}

	#####################################################  
	# FINALIZAMOS O ARQUIVO E RETORNAMOS O HTML  
	#####################################################
	$template->block("LEAD_CAPTURE"); 
	$template->show(); 