<?php
##################################################################################
# IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
##################################################################################
	include_once($_SERVER['DOCUMENT_ROOT'].'/admin/App/Lib/class-ws-v1.php');

##################################################################################
# INICIA SESSÃO
##################################################################################
	_session();

#####################################################  
#CONFIGURA DADOS GERAIS
#####################################################  
	ws::updateTool($_GET['ws_id_ferramenta']);

##################################################################################
# DEFINE AS VARIAVEIS 
##################################################################################	
	@define("WS_ID_FERRAMENTA"	,$_GET['ws_id_ferramenta']);
	@define("_ID_GALERIA_"		,$_GET['id_galeria']);
	@define("ID_ITEM"			,$_GET['id_item']);
	@define("BACK"				,$_GET['back']);
	@define("TOKEN_GROUP"		,$_GET['token_group']);
	@define("PATCH"				,$_SESSION['PATCH']);

##################################################################################
# PESQUISA NA BASE DE DADOS AS IMAGENS DA GALERIA
##################################################################################
	$S					= new MySQL();
	$S->set_table(PREFIX_TABLES . '_model_img_gal');
	$S->set_where('id_galeria="'._ID_GALERIA_.'"');
	$S->set_order('posicao','ASC');
	$S->select();	

##################################################################################
# INVOCA A CLASSE DO TEMPLATE
##################################################################################
	$_SET_TEMPLATE_INPUT = new Template(ROOT_ADMIN."/App/Templates/html/Modulos/ws-tool-galeria-fotos-template.html", true);

##################################################################################
# SETAMOS AS VARIAVEIS AO TEMPLATE
##################################################################################
	$_SET_TEMPLATE_INPUT->TOKEN_GROUP 			= TOKEN_GROUP;
	$_SET_TEMPLATE_INPUT->WS_ID_FERRAMENTA 		= WS_ID_FERRAMENTA;
	$_SET_TEMPLATE_INPUT->_TITULO_FERRAMENTA_ 	= $_SESSION['_TITULO_FERRAMENTA_'];
	$_SET_TEMPLATE_INPUT->PATCH 				= PATCH;
	$_SET_TEMPLATE_INPUT->ID_ITEM 				= ID_ITEM;
	$_SET_TEMPLATE_INPUT->_ID_GALERIA_ 			= _ID_GALERIA_;
	$_SET_TEMPLATE_INPUT->BACK 					= BACK;

##################################################################################
# VARREMOS A BASE RETORNANDO O TEMPLATE DE CADA IMAGEM
##################################################################################
	foreach($S->fetch_array as $img){
		$_SET_TEMPLATE_INPUT->ID_LI 	= $img['id'];
		$_SET_TEMPLATE_INPUT->FILE_LI 	= $img['file'];
		$_SET_TEMPLATE_INPUT->block("IMAGEM");
	}


##################################################################################
# PRINTAMOS O RESULTADO FINAL NA TELA
##################################################################################
	$_SET_TEMPLATE_INPUT->block("GALERIA_IMAGENS");
	$_SET_TEMPLATE_INPUT->show();