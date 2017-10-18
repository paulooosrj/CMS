<?php
##################################################################################
# IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
##################################################################################
	include_once(__DIR__.'/../../Lib/class-ws-v1.php');

##################################################################################
# INICIA SESSÃO
##################################################################################
	_session();

##################################################################################
# DELETA O CACHE INTERNO E CRIA UM RASCUNHO DO ÍTEM
##################################################################################
	clearstatcache();
	criaRascunho($_GET['ws_id_ferramenta'],$_GET['id_item']);

##################################################################################
# DEFINE VARIAVEIS GET 
##################################################################################
	if(empty($_GET['id_cat'])){
		$_GET['id_cat']=0;
	}

##################################################################################
# DEFINE AS VARIAVEIS 
##################################################################################	
	@define("ID_CAT"			,		$_GET['id_cat']);
	@define("ID_ITEM"			,		$_GET['id_item']);
	@define("TOKEN_GROUP"		,		$_GET['token_group']);

##################################################################################
# INVOCA A CLASSE DO TEMPLATE
##################################################################################
	$_SET_TEMPLATE_INPUT = new Template(ROOT_ADMIN."/App/Templates/html/Modulos/ws-tool-galerias-template.html", true);
	if(isset($_GET['back']) && $_GET['back']=='true'){ 
		$_SET_TEMPLATE_INPUT->block('BOT_BACK');
	}

	$_SET_TEMPLATE_INPUT->TOKEN_GROUP 			= TOKEN_GROUP;
	$_SET_TEMPLATE_INPUT->_TITULO_FERRAMENTA_ 	= "Galerias de fotos";//$_SESSION['_TITULO_FERRAMENTA_'];
	$_SET_TEMPLATE_INPUT->PATCH 				= 'App/Modulos/_modulo_';
	$_SET_TEMPLATE_INPUT->ID_ITEM 				= ID_ITEM;
	$_SET_TEMPLATE_INPUT->WS_ID_FERRAMENTA 		= $_GET['ws_id_ferramenta'];
	$_SET_TEMPLATE_INPUT->BACK 					= $_GET['back'];


##########################################################################################################
# VERIFICA SE JÁ TEM RASCUNHO
##########################################################################################################
	$draft				= new MySQL();
	$draft->set_table(PREFIX_TABLES."_model_item");
	$draft->set_where('ws_draft="1"');
	$draft->set_where('AND ws_id_draft="'.$_GET['id_item'].'"');
	$draft->select();

	$s 					= new MySQL();
	$s->set_table(PREFIX_TABLES.'_model_gal');
	$s->set_order('posicao','ASC');

	if((isset($_GET['original']) && $_GET['original']=='true')|| $draft->_num_rows==0){
		$s->set_where('id_item="'.$_GET['id_item'].'"');
	}else{
		$s->set_where('ws_draft="1"');
		$s->set_where('AND ws_id_draft="'.$_GET['id_item'].'"');
	}
	$s->select();

##########################################################################################################
# VERIFICA SE JÁ TEM RASCUNHO
##########################################################################################################
	foreach($s->fetch_array as $img){ 
		$_SET_TEMPLATE_INPUT->ID_GAL 	= $img['id'];
		$_SET_TEMPLATE_INPUT->AVATAR	= $img['avatar'];
		$_SET_TEMPLATE_INPUT->PATCH 	= 'App/Modulos/_modulo_';
		$_SET_TEMPLATE_INPUT->ID_ITEM 	= $_GET['id_item'];
		$_SET_TEMPLATE_INPUT->block("BLOCK_GALERIA");
	 }

##################################################################################
# PRINTAMOS O RESULTADO FINAL NA TELA
##################################################################################
$_SET_TEMPLATE_INPUT->block("GALERIAS");
$_SET_TEMPLATE_INPUT->show();
