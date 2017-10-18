<?php
	#####################################################  
	# FORMATA O CAMINHO ROOT
	#####################################################
	$r                        = $_SERVER["DOCUMENT_ROOT"];
	$_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;

	#####################################################  
	# DEFINE O PATH DO MÓDULO 
	#####################################################
	define("PATH", 'App/Modulos/_tools_');
		
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
	include('./../../Lib/class-ws-v1.php');

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
	define("TEMPLATE_LINK", ROOT_ADMIN . "/App/Templates/html/Modulos/_tools_/ws-tool-pages-template.html");
	
	#####################################################  
	# SEPARAMOS A VARIÁVEL DO SETUP DATA 
	#####################################################
	$setupdata = new MySQL();
	$setupdata->set_table(PREFIX_TABLES . 'setupdata');
	$setupdata->set_order('id', 'DESC');
	$setupdata->set_limit(1);
	$setupdata->debug(0);
	$setupdata->select();
	$setupdata = $setupdata->fetch_array[0];
	
	#####################################################  
	# MONTAMOS A CLASSE DOS TEMPLATES 
	#####################################################
	$template           		= new Template(TEMPLATE_LINK, true);

	#####################################################  
	# SETAMOS AS VARIÁVEIS NECESSÁRIAS 
	#####################################################
	$template->PATH 								= "App/Modulos/_tools_";
	$template->topTitle 							= ws::getlang('pages>topTitle');
	$template->buttomAddPage 						= ws::getlang('pages>buttomAddPage');
	$template->pages_iconsPage_ManageMetaTags 		= ws::getlang('pages>iconsPage>ManageMetaTags');
	$template->pages_iconsPage_MoveItem 			= ws::getlang('pages>iconsPage>MoveItem');
	$template->pages_iconsPage_Edit 				= ws::getlang('pages>iconsPage>Edit');
	$template->pages_iconsPage_EditCode 			= ws::getlang('pages>iconsPage>EditCode');
	$template->pages_iconsPage_delete 				= ws::getlang('pages>iconsPage>delete');
	$template->pages_responseMovePage_sucess 		= ws::getlang('pages>responseMovePage>sucess');
	$template->pages_responseMovePage_fail 			= ws::getlang('pages>responseMovePage>fail');
	$template->pages_modalAddPage_title 			= ws::getlang('pages>modalAddPage>title');
	$template->pages_modalAddPage_pathfile 			= ws::getlang('pages>modalAddPage>pathfile');
	$template->pages_modalAddPage_FriendlyURL 		= ws::getlang('pages>modalAddPage>FriendlyURL');
	$template->pages_modalAddPage_buttom_add 		= ws::getlang('pages>modalAddPage>button>add');
	$template->pages_modalAddPage_buttom_cancel		= ws::getlang('pages>modalAddPage>button>cancel');
	$template->pages_modalAddPage_response_sucess 	= ws::getlang('pages>modalAddPage>response>sucess');
	$template->pages_modalAddPage_response_fail 	= ws::getlang('pages>modalAddPage>response>fail');
	$template->pages_modalDetailsPage_buttom_save 	= ws::getlang('pages>modalDetailsPage>button>save');
	$template->pages_modalDetailsPage_buttom_cancel = ws::getlang('pages>modalDetailsPage>button>cancel');
	$template->pages_modalDetailsPage_SelectField	= ws::getlang('pages>modalDetailsPage>SelectField');

	$template->pages_modalDeletePage_content		= ws::getlang('pages>modalDeletePage>content');
	$template->pages_modalDeletePage_button_ok		= ws::getlang('pages>modalDeletePage>button>ok');
	$template->pages_modalDeletePage_button_cancel	= ws::getlang('pages>modalDeletePage>button>cancel');


	#####################################################  
	# BUSCAMOS NA BASE DE DADOS AS FERRAMENTAS 
	#####################################################
	$s = new MySQL();
	$s->set_table(PREFIX_TABLES."ws_ferramentas");
	$s->set_where('App_Type="0"');
	$s->set_where('AND _plugin_="0"');
	$s->set_order('posicao','ASC');
	$s->select();
	foreach($s->fetch_array as $ferramenta){
		################################################################
		# DENTRO DO FOREACH BUSCAMOS Á PAGINAS COM O TOKEN CADASTRADO 
		################################################################
		$file 					= new MySQL();
		$file->set_table(PREFIX_TABLES.'ws_pages');
		$file->set_where('token="'.$ferramenta['token'].'"');
		$file->select();
		if($file->_num_rows==1){
			$template->LI_ID 		=	$ferramenta['id'];
			$template->LI_TOKEN		=	$ferramenta['token'];
			$template->LI_FILE 		=	$file->fetch_array[0]['file'];
			$template->LI_ID_FILE 	=	$file->fetch_array[0]['id'];
			$template->LI_TITLE 	= 	$ferramenta['_tit_menu_'];	
			$template->block('LI_PAGES');
		}
	}

	################################################################
	# BLOCAMOS O CONTEUDO E PRINTAMOS O HTML
	################################################################
	$template->block('PAGES_MODEL');
	$template->show();