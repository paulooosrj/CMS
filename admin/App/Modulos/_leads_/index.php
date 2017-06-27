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
	include($_SERVER['DOCUMENT_ROOT'] . '/admin/App/Lib/class-ws-v1.php');
	
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
	define("TEMPLATE_LINK", ROOT_ADMIN . "/App/Templates/html/Modulos/_leads_/ws-tool-leads-index.html");

	#####################################################  
	# MONTAMOS A CLASSE DOS TEMPLATES 
	#####################################################
	$template           	= new Template(TEMPLATE_LINK, true);
	$template->DOMINIO 		= ws::protocolURL().DOMINIO;
	$template->PATH 		= 'App/Modulos/_leads_';
		
	$s = new MySQL();
	$s->set_table(PREFIX_TABLES.'ws_list_leads');
	$s->select();
	foreach($s->fetch_array as $img){ 
		$template->LI_TOKEN = strtolower($img['token']);
		$template->LI_TITLE = $img['title'];
		$template->LI_ID 	= $img['id']; 
		$template->block('LI_LEAD'); 
	}
	
	#####################################################  
	# BLOCO DE TRADUÇÃO
	#####################################################
	
	$template->Leads_Index_FormRegister				=	ws::getLang("Leads>Index>FormRegister");
	$template->Leads_Index_CreateLink				=	ws::getLang("Leads>Index>CreateLink");
	$template->Leads_Index_ViewRegister				=	ws::getLang("Leads>Index>ViewRegister");
	$template->Leads_Index_Edit						=	ws::getLang("Leads>Index>Edit");
	$template->Leads_Index_Delete					=	ws::getLang("Leads>Index>Delete");
	$template->Leads_Index_Modal_LoadRegister		=	ws::getLang("Leads>Index>Modal>LoadRegister");
	$template->Leads_Index_Modal_AreSure			=	ws::getLang("Leads>Index>Modal>AreSure");
	$template->Leads_Index_Modal_NotBack			=	ws::getLang("Leads>Index>Modal>NotBack");
	$template->Leads_Index_Modal_Delete				=	ws::getLang("Leads>Index>Modal>Delete");
	$template->Leads_Index_Modal_Cancel				=	ws::getLang("Leads>Index>Modal>Cancel");
	$template->Leads_Index_Modal_DeleteLink			=	ws::getLang("Leads>Index>Modal>DeleteLink");
	$template->Leads_Index_Modal_CreateLead			=	ws::getLang("Leads>Index>Modal>CreateLead");

	

	#####################################################  
	# FINALIZA O ARQUIVO, PUXA O BLOCO E RETORNA O HTML 
	#####################################################
	$template->block('LEAD_CAPTURE'); 
	$template->show(); 