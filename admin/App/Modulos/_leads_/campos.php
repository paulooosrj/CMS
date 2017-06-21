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
	define("TEMPLATE_LINK", ROOT_ADMIN . "/App/Templates/html/Modulos/_leads_/ws-tool-leads-campos.html");

	#####################################################  
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	#####################################################
	$_SESSION['token_group']	= _token(PREFIX_TABLES."ws_biblioteca","token_group");
	$_SESSION['_PATCH_']		= "App/Modulos/_leads_";

	#####################################################
	# MONTAMOS A CLASSE DOS TEMPLATES 
	#####################################################
	$template           	= new Template(TEMPLATE_LINK, true);
	$template->PATH 		= 'App/Modulos/_leads_';
	$template->TABELA 		= $_tabela_ = strtolower(PREFIX_TABLES.'wslead_'.$_GET['token_table']);
	$template->TOKEN_TABLE 	= $_GET['token_table'];

	##########################################################
	# PESQUISAMOS NA BASE OS CAMPOS CADASTRADOS NESSA CAMPANHA 
	##########################################################
	$D = new MySQL();
	$D->set_table($_tabela_);
	$D->show_columns();
	foreach($D->fetch_array as $img){
		##########################################################
		# SEPARAMOS O BLOCO 
		##########################################################
		if($img['Field']!="id"){
			$template->LI_FIELD 	= $img['Field'];
			$template->block('LI_CAMPO');
		}
	}

	##############################################################
	# FINALIZAMOS O ARQUIVO, SEPARAMOS O BLOCO E PRINTAMOS O HTML 
	##############################################################
	$template->block('CAMPOS_LEADS');
	$template->show();
